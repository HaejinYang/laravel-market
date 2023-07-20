<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;

class SellerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {
        $transactions = $seller->products()->has('transactions')->with('transactions')->get()->pluck('transactions')->collapse();

        return $this->showAll($transactions);
    }
}
