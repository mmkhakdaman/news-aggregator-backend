<?php

namespace App\Services;

use App\Contracts\ArticelIterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class NewsCred implements ArticelIterface
{
    private $apiKey;
    private $apiUrl = 'https://api.newscred.com/v2/';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function search(string $query): array
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->apiUrl, [
            'q' => $query,
            'api-key' => $this->apiKey,
        ]);

        dd($request->json());

        $results = $request->json()['response']['results'];


        $results = array_map(function ($result) {
            return [
                'title' => $result['webTitle'],
                'url' => $result['webUrl'],
                'source' => 'Guardian',
                'category' => $result['sectionName'],
                'author' => $result['fields']['byline'] ?? '',
                'published_at' => Carbon::parse($result['webPublicationDate'])->diffForHumans(),
            ];
        }, $results);

        return $results;
    }
}
