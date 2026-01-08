<?php

namespace App\Http\Controllers;

use App\Models\User;

class PageController extends Controller
{
    public function about()
    {
        $author = User::query()->first();

        return view('pages.about', [
            'author' => $author,
        ]);
    }
}
