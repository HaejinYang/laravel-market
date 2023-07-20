<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;

class CategoryBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        $buyers = $category->products()->has('transactions')->with('transactions.buyer')
            ->get()->pluck('transactions')->collapse()->pluck('buyer')->unique('id')->values(0);

        return $this->showAll($buyers);
    }
}
