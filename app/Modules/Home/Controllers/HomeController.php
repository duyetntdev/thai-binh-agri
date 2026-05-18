<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Controller;
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
}
