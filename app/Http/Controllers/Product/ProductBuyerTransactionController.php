<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => ['required', 'integer', 'min:1'],
        ];

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse("구매자는 판매자와 달라야 합니다.", Response::HTTP_CONFLICT);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse("구매자는 인증된 유저여야 합니다.", Response::HTTP_CONFLICT);
        }

        if (!$product->seller->isVerified()) {
            return $this->errorResponse("판매자는 인증된 유저여야 합니다.", Response::HTTP_FORBIDDEN);
        }

        if (!$product->isAvailable()) {
            return $this->errorResponse("상품이 사용가능한 상태가 아닙니다.", Response::HTTP_FORBIDDEN);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse("상품 재고가 주문량보다 적습니다", Response::HTTP_FORBIDDEN);
        }

        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction);
        });
    }
}
