<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateFeed(Request $request)
    {
        $user = $request->user();

        $categories = $request->input('categories');
        $sources = $request->input('sources');
        $authors = $request->input('authors');

        $user->update([
            'preferences' => [
                'categories' => $categories,
                'sources' => $sources,
                'authors' => $authors,
            ],
        ]);

        return response()->json($user);
    }
}
