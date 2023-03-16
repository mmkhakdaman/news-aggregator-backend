<?php

namespace App\Services;

use App\Contracts\ArticelIterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class Guardian implements ArticelIterface
{
    private $apiUrl = 'https://content.guardianapis.com';
    private $params = [];
    private $filter = [];

    public function __construct(string $apiKey)
    {
        $this->params['api-key'] = $apiKey;
        $this->filter = $this->params;
    }

    public function filterByKeyword(string $query): self
    {
        $this->filter['q'] = $query;

        return $this;
    }

    public function filterBySource(string|array|null $source): self
    {
        // set null if it include Guardian
        if (is_array($source)) {
            if (in_array('guardian', $source)) {
                $source = null;
            }
        } else {
            if ($source == 'guardian') {
                $source = null;
            }
        }
        $this->filter['source'] = $source;

        return $this;
    }

    public function filterByCategory(string|array|null $category): self
    {
        if (!empty($category)) {
            if (is_array($category)) {
                $category = implode('|', $category);
            }
            $category = str_replace('&', '|', $category);
            $category = str_replace(' ', '', $category);
            // $this->filter['section'] = $category;
        }


        return $this;
    }

    public function filterByAuthor(string|array|null $author): self
    {
        // if (!empty($author)) {
        //     if (is_array($author)) {
        //         $author = implode(',', $author);
        //     }
        //     $this->filter['show-tags'] = 'contributor';
        //     $this->filter['tag'] = "contributor/$author";
        //     // $this->filter['author'] = $author;
        // }

        return $this;
    }

    public function setPage(int $page = 1): self
    {
        $this->filter['page'] = $page;
        return $this;
    }

    public function get(): array
    {
        try {
            if (!empty($this->filter['source'])) {
                return [];
            }

            $request = Http::get("$this->apiUrl/search", $this->filter);

            $results = $request->json()['response']['results'];

            $results = array_map(function ($result) {
                return [
                    'title' => $result['webTitle'],
                    'url' => $result['webUrl'],
                    'source' => 'Guardian',
                    'author' => $result['fields']['byline'] ?? '',
                    'category' => $result['sectionName'],
                    'published_at' => Carbon::parse($result['webPublicationDate'])->format('Y-m-d'),
                ];
            }, $results);

            return $results;
        } catch (\Exception $e) {
            $results = [];
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
                'id' => 'guardian',
                'name' => 'Guardian',
                // 'source' => self::class,
            ],
        ];

        return $results;
    }

    public function getCategories(): array
    {
        $request = Http::get("$this->apiUrl/sections", $this->params);

        $results = $request->json()['response']['results'];

        $results = array_map(function ($result) {
            return [
                'id' => $result['id'],
                'name' => $result['webTitle'],
                // 'source' => self::class
            ];
        }, $results);

        return $results;
    }

    public function getAuthors(): array
    {
        $results = [
            // [
            //     'id' => 'guardian',
            //     'name' => 'Guardian',
            //     'source' => self::class,
            // ],
        ];

        return $results;
    }
}
