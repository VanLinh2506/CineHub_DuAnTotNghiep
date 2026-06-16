<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dumpPath = $root . DIRECTORY_SEPARATOR . 'cinehub (3).sql';
$databaseDir = $root . DIRECTORY_SEPARATOR . 'database';
$targetPath = $databaseDir . DIRECTORY_SEPARATOR . 'database.sqlite';
$tempPath = $databaseDir . DIRECTORY_SEPARATOR . 'database.import.sqlite';
$backupPath = $databaseDir . DIRECTORY_SEPARATOR . 'database.sqlite.bak-' . date('Ymd-His');

if (! file_exists($dumpPath)) {
    fwrite(STDERR, "SQL dump not found: {$dumpPath}\n");
    exit(1);
}

if (file_exists($tempPath) && ! unlink($tempPath)) {
    fwrite(STDERR, "Unable to remove temp database: {$tempPath}\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $tempPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->exec('PRAGMA foreign_keys = OFF');
$pdo->beginTransaction();

$sql = file_get_contents($dumpPath);

if ($sql === false) {
    fwrite(STDERR, "Unable to read SQL dump.\n");
    exit(1);
}

$sql = preg_replace('/^\s*--.*$/m', '', $sql);
$sql = preg_replace('/^\s*\/\*![\s\S]*?\*\/\s*;?\s*$/m', '', $sql);
$statements = splitSqlStatements($sql);

$tableCount = 0;
$rowCount = 0;
$indexCount = 0;

foreach ($statements as $statement) {
    $statement = trim($statement);

    if ($statement === '' || shouldSkipStatement($statement)) {
        continue;
    }

    if (stripos($statement, 'CREATE TABLE') === 0) {
        createSqliteTable($pdo, $statement);
        $tableCount++;
        continue;
    }

    if (stripos($statement, 'INSERT INTO') === 0) {
        $rowCount += importInsertStatement($pdo, $statement);
        continue;
    }

    if (stripos($statement, 'ALTER TABLE') === 0) {
        $indexCount += applyAlterIndexes($pdo, $statement);
    }
}

createLaravelSupportTables($pdo);
seedMigrationsTable($pdo);

$pdo->commit();
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo = null;

if (file_exists($targetPath) && ! rename($targetPath, $backupPath)) {
    fwrite(STDERR, "Unable to backup existing database to: {$backupPath}\n");
    exit(1);
}

if (! rename($tempPath, $targetPath)) {
    if (file_exists($backupPath)) {
        rename($backupPath, $targetPath);
    }

    fwrite(STDERR, "Unable to replace SQLite database.\n");
    exit(1);
}

echo "Imported {$tableCount} tables, {$rowCount} rows, {$indexCount} indexes into SQLite.\n";
echo "Backup saved to {$backupPath}\n";

function shouldSkipStatement(string $statement): bool
{
    $prefixes = [
        'SET ',
        'START TRANSACTION',
        'COMMIT',
        'CREATE DATABASE',
        'USE ',
        'LOCK TABLES',
        'UNLOCK TABLES',
    ];

    foreach ($prefixes as $prefix) {
        if (stripos($statement, $prefix) === 0) {
            return true;
        }
    }

    return false;
}

function splitSqlStatements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $inBacktick = false;
    $escaped = false;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $buffer .= $char;

        if ($inSingleQuote) {
            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === "'") {
                $inSingleQuote = false;
            }

            continue;
        }

        if ($inDoubleQuote) {
            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === '"') {
                $inDoubleQuote = false;
            }

            continue;
        }

        if ($inBacktick) {
            if ($char === '`') {
                $inBacktick = false;
            }

            continue;
        }

        if ($char === "'") {
            $inSingleQuote = true;
            continue;
        }

        if ($char === '"') {
            $inDoubleQuote = true;
            continue;
        }

        if ($char === '`') {
            $inBacktick = true;
            continue;
        }

        if ($char === ';') {
            $statements[] = $buffer;
            $buffer = '';
        }
    }

    if (trim($buffer) !== '') {
        $statements[] = $buffer;
    }

    return $statements;
}

function createSqliteTable(PDO $pdo, string $statement): void
{
    if (! preg_match('/^CREATE TABLE\s+`?([^`(]+)`?\s*\((.*)\)\s*(ENGINE=.*)?$/is', trim($statement), $matches)) {
        return;
    }

    $table = trim($matches[1]);
    $body = trim($matches[2]);
    $parts = splitTopLevelComma($body);
    $columns = [];

    foreach ($parts as $part) {
        $part = trim($part);

        if ($part === '' || $part[0] !== '`') {
            continue;
        }

        if (preg_match('/^`([^`]+)`\s+(.*)$/s', $part, $columnMatches)) {
            $columns[] = convertColumnDefinition($columnMatches[1], rtrim(trim($columnMatches[2]), ','));
        }
    }

    if ($columns === []) {
        return;
    }

    $sql = 'CREATE TABLE IF NOT EXISTS "' . $table . '" (' . implode(', ', $columns) . ')';

    try {
        $pdo->exec($sql);
    } catch (PDOException $exception) {
        throw new RuntimeException("Failed creating table [{$table}] with SQL: {$sql}", 0, $exception);
    }
}

function convertColumnDefinition(string $name, string $definition): string
{
    $normalized = preg_replace('/\s+/', ' ', $definition);
    $type = 'TEXT';

    if (preg_match('/^([a-zA-Z]+)(\(([^)]*)\))?/i', $normalized, $typeMatches)) {
        $type = mapSqliteType(strtolower($typeMatches[1]));
    }

    $constraints = [];
    $hasAutoIncrement = stripos($normalized, 'AUTO_INCREMENT') !== false;
    $isPrimaryKey = stripos($normalized, 'PRIMARY KEY') !== false;

    if ($name === 'id' || $hasAutoIncrement || $isPrimaryKey) {
        return '"' . $name . '" INTEGER PRIMARY KEY';
    }

    if (stripos($normalized, 'NOT NULL') !== false) {
        $constraints[] = 'NOT NULL';
    }

    if (preg_match('/DEFAULT\s+((?:\'(?:\\\\.|[^\'])*\')|[^,\s]+)/i', $normalized, $defaultMatches)) {
        $default = normalizeDefaultValue($defaultMatches[1]);

        if ($default !== '') {
            $constraints[] = 'DEFAULT ' . $default;
        }
    }

    return '"' . $name . '" ' . $type . ($constraints ? ' ' . implode(' ', $constraints) : '');
}

function mapSqliteType(string $mysqlType): string
{
    return match ($mysqlType) {
        'int', 'integer', 'tinyint', 'smallint', 'mediumint', 'bigint' => 'INTEGER',
        'decimal', 'numeric', 'float', 'double', 'real' => 'REAL',
        'blob', 'binary', 'varbinary', 'longblob', 'mediumblob', 'tinyblob' => 'BLOB',
        default => 'TEXT',
    };
}

function normalizeDefaultValue(string $value): string
{
    $value = trim(rtrim($value, ','));
    $upper = strtoupper($value);

    if ($upper === 'NULL') {
        return 'NULL';
    }

    if ($upper === 'CURRENT_TIMESTAMP' || $upper === 'CURRENT_TIMESTAMP()') {
        return 'CURRENT_TIMESTAMP';
    }

    return $value;
}

function splitTopLevelComma(string $sql): array
{
    $parts = [];
    $buffer = '';
    $depth = 0;
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $inBacktick = false;
    $escaped = false;
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];

        if ($inSingleQuote) {
            $buffer .= $char;

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === "'") {
                $inSingleQuote = false;
            }

            continue;
        }

        if ($inDoubleQuote) {
            $buffer .= $char;

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === '"') {
                $inDoubleQuote = false;
            }

            continue;
        }

        if ($inBacktick) {
            $buffer .= $char;

            if ($char === '`') {
                $inBacktick = false;
            }

            continue;
        }

        if ($char === "'") {
            $inSingleQuote = true;
            $buffer .= $char;
            continue;
        }

        if ($char === '"') {
            $inDoubleQuote = true;
            $buffer .= $char;
            continue;
        }

        if ($char === '`') {
            $inBacktick = true;
            $buffer .= $char;
            continue;
        }

        if ($char === '(') {
            $depth++;
            $buffer .= $char;
            continue;
        }

        if ($char === ')') {
            $depth--;
            $buffer .= $char;
            continue;
        }

        if ($char === ',' && $depth === 0) {
            $parts[] = $buffer;
            $buffer = '';
            continue;
        }

        $buffer .= $char;
    }

    if (trim($buffer) !== '') {
        $parts[] = $buffer;
    }

    return $parts;
}

function importInsertStatement(PDO $pdo, string $statement): int
{
    if (! preg_match('/^INSERT INTO\s+`?([^`]+)`?\s*\((.*?)\)\s*VALUES\s*(.+)$/is', trim($statement), $matches)) {
        return 0;
    }

    $table = trim($matches[1]);
    $columns = array_map(
        static fn (string $column): string => trim($column, " \t\n\r\0\x0B`"),
        splitTopLevelComma($matches[2])
    );
    $rows = parseInsertRows($matches[3]);

    if ($rows === []) {
        return 0;
    }

    $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
    $sql = 'INSERT INTO "' . $table . '" ("' . implode('", "', $columns) . '") VALUES ' . $placeholders;
    $statement = $pdo->prepare($sql);

    foreach ($rows as $row) {
        $statement->execute($row);
    }

    return count($rows);
}

function parseInsertRows(string $valuesSql): array
{
    $rows = [];
    $row = [];
    $field = '';
    $level = 0;
    $inString = false;
    $escaped = false;
    $length = strlen($valuesSql);

    for ($i = 0; $i < $length; $i++) {
        $char = $valuesSql[$i];

        if ($inString) {
            $field .= $char;

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === "'") {
                $inString = false;
            }

            continue;
        }

        if ($char === "'") {
            $inString = true;
            $field .= $char;
            continue;
        }

        if ($char === '(') {
            if ($level > 0) {
                $field .= $char;
            }

            $level++;
            continue;
        }

        if ($char === ')') {
            $level--;

            if ($level === 0) {
                $row[] = parseMysqlLiteral(trim($field));
                $rows[] = $row;
                $row = [];
                $field = '';
                continue;
            }

            $field .= $char;
            continue;
        }

        if ($char === ',' && $level === 1) {
            $row[] = parseMysqlLiteral(trim($field));
            $field = '';
            continue;
        }

        if ($level > 0) {
            $field .= $char;
        }
    }

    return $rows;
}

function parseMysqlLiteral(string $value): mixed
{
    if ($value === '') {
        return null;
    }

    if (strcasecmp($value, 'NULL') === 0) {
        return null;
    }

    if ($value[0] === "'" && substr($value, -1) === "'") {
        return decodeMysqlString(substr($value, 1, -1));
    }

    if (preg_match('/^-?\d+$/', $value)) {
        return (int) $value;
    }

    if (is_numeric($value)) {
        return (float) $value;
    }

    return $value;
}

function decodeMysqlString(string $value): string
{
    $result = '';
    $length = strlen($value);

    for ($i = 0; $i < $length; $i++) {
        $char = $value[$i];

        if ($char !== '\\' || $i === $length - 1) {
            $result .= $char;
            continue;
        }

        $i++;
        $escaped = $value[$i];

        $result .= match ($escaped) {
            '0' => "\0",
            'b' => "\x08",
            'n' => "\n",
            'r' => "\r",
            't' => "\t",
            'Z' => "\x1A",
            '\\' => '\\',
            "'" => "'",
            '"' => '"',
            default => $escaped,
        };
    }

    return $result;
}

function applyAlterIndexes(PDO $pdo, string $statement): int
{
    if (! preg_match('/^ALTER TABLE\s+`?([^`]+)`?\s+(.*)$/is', trim($statement), $matches)) {
        return 0;
    }

    $table = trim($matches[1]);
    $clauses = splitTopLevelComma($matches[2]);
    $count = 0;

    foreach ($clauses as $clause) {
        $clause = trim($clause);

        if (preg_match('/^ADD UNIQUE KEY\s+`([^`]+)`\s*\((.+)\)$/i', $clause, $keyMatches)) {
            $indexName = $table . '_' . $keyMatches[1];
            $columns = normalizeIndexColumns($keyMatches[2]);
            $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS "' . $indexName . '" ON "' . $table . '" (' . $columns . ')');
            $count++;
            continue;
        }

        if (preg_match('/^ADD KEY\s+`([^`]+)`\s*\((.+)\)$/i', $clause, $keyMatches)) {
            $indexName = $table . '_' . $keyMatches[1];
            $columns = normalizeIndexColumns($keyMatches[2]);
            $pdo->exec('CREATE INDEX IF NOT EXISTS "' . $indexName . '" ON "' . $table . '" (' . $columns . ')');
            $count++;
        }
    }

    return $count;
}

function normalizeIndexColumns(string $columns): string
{
    $parts = array_map(
        static fn (string $column): string => '"' . trim(preg_replace('/\(\d+\)/', '', $column), " \t\n\r\0\x0B`") . '"',
        splitTopLevelComma($columns)
    );

    return implode(', ', $parts);
}

function createLaravelSupportTables(PDO $pdo): void
{
    $statements = [
        'CREATE TABLE IF NOT EXISTS "migrations" ("id" INTEGER PRIMARY KEY, "migration" TEXT NOT NULL, "batch" INTEGER NOT NULL)',
        'CREATE TABLE IF NOT EXISTS "password_reset_tokens" ("email" TEXT PRIMARY KEY, "token" TEXT NOT NULL, "created_at" TEXT)',
        'CREATE TABLE IF NOT EXISTS "sessions" ("id" TEXT PRIMARY KEY, "user_id" INTEGER, "ip_address" TEXT, "user_agent" TEXT, "payload" TEXT NOT NULL, "last_activity" INTEGER NOT NULL)',
        'CREATE INDEX IF NOT EXISTS "sessions_user_id_index" ON "sessions" ("user_id")',
        'CREATE INDEX IF NOT EXISTS "sessions_last_activity_index" ON "sessions" ("last_activity")',
        'CREATE TABLE IF NOT EXISTS "cache" ("key" TEXT PRIMARY KEY, "value" TEXT NOT NULL, "expiration" INTEGER NOT NULL)',
        'CREATE INDEX IF NOT EXISTS "cache_expiration_index" ON "cache" ("expiration")',
        'CREATE TABLE IF NOT EXISTS "cache_locks" ("key" TEXT PRIMARY KEY, "owner" TEXT NOT NULL, "expiration" INTEGER NOT NULL)',
        'CREATE INDEX IF NOT EXISTS "cache_locks_expiration_index" ON "cache_locks" ("expiration")',
        'CREATE TABLE IF NOT EXISTS "jobs" ("id" INTEGER PRIMARY KEY, "queue" TEXT NOT NULL, "payload" TEXT NOT NULL, "attempts" INTEGER NOT NULL, "reserved_at" INTEGER, "available_at" INTEGER NOT NULL, "created_at" INTEGER NOT NULL)',
        'CREATE INDEX IF NOT EXISTS "jobs_queue_index" ON "jobs" ("queue")',
        'CREATE TABLE IF NOT EXISTS "job_batches" ("id" TEXT PRIMARY KEY, "name" TEXT NOT NULL, "total_jobs" INTEGER NOT NULL, "pending_jobs" INTEGER NOT NULL, "failed_jobs" INTEGER NOT NULL, "failed_job_ids" TEXT NOT NULL, "options" TEXT, "cancelled_at" INTEGER, "created_at" INTEGER NOT NULL, "finished_at" INTEGER)',
        'CREATE TABLE IF NOT EXISTS "failed_jobs" ("id" INTEGER PRIMARY KEY, "uuid" TEXT NOT NULL, "connection" TEXT NOT NULL, "queue" TEXT NOT NULL, "payload" TEXT NOT NULL, "exception" TEXT NOT NULL, "failed_at" TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP)',
        'CREATE UNIQUE INDEX IF NOT EXISTS "failed_jobs_uuid_unique" ON "failed_jobs" ("uuid")',
        'CREATE TABLE IF NOT EXISTS "news_categories" ("id" INTEGER PRIMARY KEY, "name" TEXT NOT NULL, "slug" TEXT NOT NULL, "created_at" TEXT, "updated_at" TEXT)',
        'CREATE UNIQUE INDEX IF NOT EXISTS "news_categories_slug_unique" ON "news_categories" ("slug")',
        'CREATE TABLE IF NOT EXISTS "news" ("id" INTEGER PRIMARY KEY, "news_category_id" INTEGER, "user_id" INTEGER, "title" TEXT NOT NULL, "slug" TEXT NOT NULL, "thumbnail" TEXT, "excerpt" TEXT, "content" TEXT NOT NULL, "status" TEXT NOT NULL DEFAULT \'draft\', "published_at" TEXT, "wp_id" TEXT, "created_at" TEXT, "updated_at" TEXT)',
        'CREATE UNIQUE INDEX IF NOT EXISTS "news_slug_unique" ON "news" ("slug")',
        'CREATE INDEX IF NOT EXISTS "news_status_published_at_index" ON "news" ("status", "published_at")',
    ];

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

function seedMigrationsTable(PDO $pdo): void
{
    $pdo->exec('DELETE FROM "migrations"');

    $migrations = [
        '0001_01_01_000000_create_users_table',
        '0001_01_01_000001_create_cache_table',
        '0001_01_01_000002_create_jobs_table',
        '2026_06_02_000001_create_news_table',
        '2026_06_08_000001_create_cinehub_core_tables',
        '2026_06_08_000002_create_booking_tables',
    ];

    $statement = $pdo->prepare('INSERT INTO "migrations" ("migration", "batch") VALUES (?, 1)');

    foreach ($migrations as $migration) {
        $statement->execute([$migration]);
    }
}
