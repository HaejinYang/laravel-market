<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;

class BuyerProductController extends ApiController
{
    /**
     * buyer와 product 모델은 직접적으로 연결되어 있지 않다.
     * buyer has many transactions, transactions belongs to buyer
     * transaction은 belongs to product, product has many transactions
     * 기존에 다루던 관계보다 한 단계 복잡해졌다. 이 때 필요한 것이 eager loading
     */
    public function index(Buyer $buyer)
    {
        $products = $buyer->transactions()->with('product')->get()->pluck('product');

        return $this->showAll($products);
    }
}
