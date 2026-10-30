<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Services\BlogService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private readonly BlogService $blogService) {}

    public function index(): View
    {
        $blogs = $this->blogService->published();

        return view('blog::index', compact('blogs'));
    }

    public function show(string $slug): View
    {
        $blog = $this->blogService->findPublishedBySlug($slug);

        return view('blog::show', compact('blog'));
    }
}
