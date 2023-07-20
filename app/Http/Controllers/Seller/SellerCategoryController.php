<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;

class SellerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {
        $categories = $seller->products()->has('categories')->with('categories')->get()->pluck('categories')->collapse()->unique('id')->values();

        return $this->showAll($categories);
    }
}
