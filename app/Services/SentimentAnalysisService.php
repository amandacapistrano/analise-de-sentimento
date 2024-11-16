<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SentimentAnalysisService
{
    public function analyze($text)
    {
        $apiKey = config('services.huggingface.api_key');
        $endpoint = config('services.huggingface.endpoint');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'inputs' => $text,
        ]);

        if ($response->failed()) {
            return ['error' => 'Erro ao chamar a API.'];
        }

        return $response->json();
    }
}
