<?php

namespace App\Http\Controllers\Frontend;

use App\Models\News;

class NewsController
{
// In NewsController.php or wherever you handle the news index page
    public function index()
    {
        $news = News::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(8); // 4 items per row × 2 rows = 8 items per page

        return view('frontend.contents.news', compact( 'news'));
    }

}
