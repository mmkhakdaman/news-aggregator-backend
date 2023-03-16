<?php

namespace App\Services;

use App\Contracts\ArticelIterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewsAPI implements ArticelIterface
{
    private $filter;
    private $params;
    private $is_search = false;
    private $apiUrl = 'https://newsapi.org/v2';


    public function __construct(string $apiKey)
    {
        $this->params['apiKey'] = $apiKey;
        $this->filter = $this->params;
    }

    public function filterByKeyword(string $query): self
    {
        $this->filter['q'] = $query;
        $this->is_search = true;

        return $this;
    }

    public function filterBySource(string|array|null $source): self
    {
        if (is_array($source)) {
            $source = implode(',', $source);
        }

        $this->filter['sources'] = $source;

        return $this;
    }

    public function filterByCategory(string|array|null $category): self
    {
        // if (is_array($category)) {
        //     $category = implode(',', $category);
        // }

        // $this->filter['category'] = $category;

        return $this;
    }

    public function filterByAuthor(string|array|null $author): self
    {
        // if (is_array($author)) {
        //     $author = implode(',', $author);
        // }

        // $this->filter['author'] = $author;

        return $this;
    }

    public function setPage(int $page = 1): self
    {
        $this->filter['page'] = $page;

        return $this;
    }


    public function search(): array
    {
        try {
            $request = Http::get("$this->apiUrl/everything", $this->filter);

            $results = $request->json()['articles'];

            $results = array_map(function ($result) {
                return [
                    'title' => $result['title'],
                    'url' => $result['url'],
                    'source' => $result['source']['name'] ?? '',
                    'author' => $result['author'] ?? '',
                    'category' => $result['category'] ?? '',
                    'published_at' => Carbon::parse($result['publishedAt'])->format('Y-m-d'),
                ];
            }, $results);

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function get(): array
    {
        try {
            $request = Http::get("$this->apiUrl/top-headlines", $this->filter);

            $results = $request->json()['articles'];

            $results = array_map(function ($result) {
                return [
                    'title' => $result['title'],
                    'url' => $result['url'],
                    'source' => $result['source']['name'] ?? '',
                    'author' => $result['author'] ?? '',
                    'category' => $result['category'] ?? '',
                    'published_at' => Carbon::parse($result['publishedAt'])->format('Y-m-d'),
                ];
            }, $results);

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getSources(): array
    {
        $request = Http::get("$this->apiUrl/top-headlines/sources", $this->params);

        $results = $request->json()['sources'];

        $results = array_map(function ($result) {
            return [
                'id' => $result['id'],
                'name' => $result['name'],
                // 'source' => self::class,
            ];
        }, $results);

        return $results;
    }

    public function getCategories(): array
    {
        $request = Http::get("$this->apiUrl/top-headlines/sources", $this->params);

        $results = $request->json()['sources'];

        $results = array_map(function ($result) {
            return $result['category'];
        }, $results);

        $results = array_map(function ($result) {
            return [
                'id' => $result,
                'name' => $result,
                // 'source' => self::class
            ];
        }, array_unique($results));

        return $results;
    }

    public function getAuthors(): array
    {

        $request = Http::get("$this->apiUrl/top-headlines/sources", $this->params);

        $results = $request->json()['sources'];

        $results = array_map(function ($result) {
            return [
                'id' => $result['id'],
                'name' => $result['name'],
                // 'source' => self::class,
            ];
        }, $results);

        return $results;
    }
}
