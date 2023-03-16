<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ArticleService;
use App\Services\Guardian;
use App\Services\NewsAPI;
use App\Services\NewsCred;
use App\Services\NewYorkTimes;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $data_sources = [
        'news_api' => NewsAPI::class,
        'new_york_times' => NewYorkTimes::class,
        'guardian' => Guardian::class,
    ];
    public function search(Request $request)
    {
        $query = $request->input('q');

        $results = [];

        foreach ($this->data_sources as $key => $class) {
            $results = array_merge($results, app($class)->filterByKeyword($query)->search());
        }

        // sort by published_at
        usort($results, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        return response()->json($results);
    }

    public function feed()
    {
        $results = [];

        $page = request()->input('page', 1);

        $user_preferences = null;
        if ($user = auth()->guard('sanctum')->user()) {
            $user_preferences = $user->preferences;
        }


        foreach ($this->data_sources as $class) {
            $results = array_merge(
                $results,
                app($class)
                    ->filterBySource($user_preferences['sources'] ?? [])
                    ->filterByCategory($user_preferences['categories'] ?? [])
                    ->filterByAuthor($user_preferences['authors'] ?? [])
                    ->setPage($page)
                    ->get()
            );
        }

        // sort by published_at
        usort($results, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        return response()->json($results);
    }


    public function sources()
    {
        $results = [];

        foreach ($this->data_sources as $class) {
            $results = array_merge($results, app($class)->getSources());
        }

        $results = array_values(array_map("unserialize", array_unique(array_map("serialize", $results))));

        return response()->json($results);
    }

    public function categories()
    {
        $results = [];

        foreach ($this->data_sources as $class) {
            $results = array_merge($results, app($class)->getCategories());
        }

        $results = array_values(array_map("unserialize", array_unique(array_map("serialize", $results))));

        return response()->json($results);
    }

    public function authors()
    {
        $results = [];


        foreach ($this->data_sources as $class) {
            $results = array_merge($results, app($class)->getAuthors());
        }


        return response()->json($results);
    }
}
