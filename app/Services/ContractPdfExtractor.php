<?php

namespace App\Services;

use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class ContractPdfExtractor
{
    public function extract(string $path): array
    {
        $text = trim((new Parser())->parseFile($path)->getText());

        if (mb_strlen($text) < 30) {
            return ['text' => $text, 'needs_ocr' => true];
        }

        return [
            'text' => $text,
            'needs_ocr' => false,
            'theater_name' => $this->match($text, '/(?:B[eê]n\s*A\s*\([^)]*\)|T[eê]n\s*r[aạ]p)\s*[:\t]\s*([^\r\n]+)/iu'),
            'start_date' => $this->date($text, '/(?:B[aắ]t\s*[đd][aầ]u|Ng[aà]y\s*b[aắ]t\s*[đd][aầ]u)\s*[:\t]\s*(\d{1,2}[\/.-]\d{1,2}[\/.-]\d{4})/iu'),
            'end_date' => $this->date($text, '/(?:K[eế]t\s*th[uú]c|Ng[aà]y\s*k[eế]t\s*th[uú]c)\s*[:\t]\s*(\d{1,2}[\/.-]\d{1,2}[\/.-]\d{4})/iu'),
        ];
    }

    private function match(string $text, string $pattern): ?string
    {
        return preg_match($pattern, $text, $matches) ? trim($matches[1]) : null;
    }

    private function date(string $text, string $pattern): ?string
    {
        $value = $this->match($text, $pattern);
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', str_replace(['.', '-'], '/', $value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
