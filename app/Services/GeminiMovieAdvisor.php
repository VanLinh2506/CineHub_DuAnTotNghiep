<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiMovieAdvisor
{
    public function ask(string $prompt): array
    {
        $key = (string) config('services.gemini.key');
        $model = (string) config('services.gemini.model', 'gemini-flash-latest');

        if ($key === '') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $response = Http::connectTimeout(6)
            ->timeout(18)
            ->retry(1, 300)
            ->acceptJson()
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($key), [
                'contents' => [[
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.65,
                    'maxOutputTokens' => 450,
                    'responseMimeType' => 'application/json',
                    'thinkingConfig' => [
                        'thinkingLevel' => 'MINIMAL',
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Gemini request failed with status ' . $response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        $decoded = null;
        if (is_string($text)) {
            $cleanText = trim($text);
            $cleanText = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleanText) ?? $cleanText;
            $decoded = json_decode($cleanText, true);

            if (!is_array($decoded) && preg_match('/\{.*\}/s', $cleanText, $matches)) {
                $decoded = json_decode($matches[0], true);
            }
        }

        if (!is_array($decoded) || !isset($decoded['reply'])) {
            $fallbackText = is_string($text)
                ? trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text) ?? $text)
                : '';

            if (preg_match('/["\']reply["\']\s*:\s*["\']([^"\']*)/isu', $fallbackText, $replyMatch)) {
                $fallbackText = trim($replyMatch[1]);
            } elseif (str_starts_with($fallbackText, '{') || str_starts_with($fallbackText, '[')) {
                $fallbackText = '';
            }

            if ($fallbackText === '') {
                throw new RuntimeException('Gemini returned an incomplete response.');
            }

            return [
                'reply' => $fallbackText,
                'movie_ids' => [],
            ];
        }

        return $decoded;
    }
}
