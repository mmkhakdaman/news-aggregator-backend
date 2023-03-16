<?php

namespace App\Services;

use App\Contracts\ArticelIterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewYorkTimes implements ArticelIterface
{
    private $apiUrl = 'https://api.nytimes.com/svc/search/v2';
    private $filter = [];

    public function __construct(string $apiKey)
    {
        $this->filter['api-key'] = $apiKey;
        $this->filter['fq'] = null;
    }

    public function filterByKeyword(string $query): self
    {
        $this->filter['q'] = $query;
        return $this;
    }

    public function filterBySource(string|array|null $source): self
    {
        if (!empty($source)) {

            if (is_array($source)) {
                $source = implode('","', $source);
            }

            if (!empty($this->filter['fq'])) {
                $this->filter['fq'] .= " AND ";
            }

            $this->filter['fq'] .= 'source:("' . $source . '")';
        }
        return $this;
    }

    public function filterByCategory(string|array|null $category): self
    {
        if (!empty($category)) {

            if (is_array($category)) {
                $category = implode('","', $category);
            }

            if (!empty($this->filter['fq'])) {
                $this->filter['fq'] .= " AND ";
            }

            $this->filter['fq'] .= 'news_desk:("' . $category . '")';
        }

        return $this;
    }

    public function filterByAuthor(string|array|null $author): self
    {
        if (!empty($author)) {

            if (is_array($author)) {
                $author = implode(",", $author);
            }

            if (!empty($this->filter['fq'])) {
                $this->filter['fq'] .= " AND ";
            }

            $this->filter['fq'] .= 'byline:("' . $author . '")';
        }

        return $this;
    }

    public function setPage(int $page = 1): self
    {
        $this->filter['page'] = $page - 1;
        return $this;
    }

    public function get(): array
    {
        try {
            $this->filter['sort'] = 'newest';
            $this->filter['fl'] = 'headline,web_url,source,byline,news_desk,pub_date';

            $request = Http::get(
                "$this->apiUrl/articlesearch.json",
                $this->filter
            );

            $results = $request->json()['response']['docs'];

            $results = array_map(
                function ($result) {
                    return [
                        'title' => $result['headline']['main'],
                        'url' => $result['web_url'],
                        'source' => $result['source'] ?? '',
                        'author' => $result['byline']['original'] ?? '',
                        'category' => $result['news_desk'],
                        'published_at' => Carbon::parse($result['pub_date'])->format('Y-m-d'),
                    ];
                },
                $results
            );

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function search(): array
    {
        return $this->get();
    }
    public function getSources(): array
    {
        $results = [
            [
                'id' => 'The New York Times',
                'name' => 'The New York Times',
                // 'source' => self::class,
            ],
        ];

        return $results;
    }

    public function getCategories(): array
    {
        try {

            $request = Http::get(
                "https://api.nytimes.com/svc/news/v3/content/section-list.json",
                $this->filter
            );

            $results = $request->json()['results'];

            $results = array_map(function ($result) {
                return [
                    'id' => $result['section'],
                    'name' => $result['display_name'],
                    // 'source' => self::class,
                ];
            }, $results);

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAuthors(): array
    {
        $results = [
            [
                'id' => 'The New York Times',
                'name' => 'The New York Times',
                // 'source' => self::class,
            ],
        ];

        return $results;
    }
}
