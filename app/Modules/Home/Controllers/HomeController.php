<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Modules\Home\Services\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService) {}

    public function index(): View
    {
        return view('home::index', [
            'bestSellers'       => $this->homeService->bestSellers(),
            'mostViewed'        => $this->homeService->mostViewed(),
            'recentlyPurchased' => $this->homeService->recentlyPurchased(),
            'categories'        => $this->homeService->categories(),
        ]);
    }

    public function about(): View
    {
        return view('home::about');
    }

    public function news(): View
    {
        return view('home::news');
    }

    public function policy(): View
    {
        return view('home::policy');
    }

    public function contact(): View
    {
        return view('home::contact');
    }
}
