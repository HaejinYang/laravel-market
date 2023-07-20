<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }

    /*
     * 두 번째 인자가 Seller아 아니라 User인 이유는 Seller 되려면 우선 Product가 하나라도 있어야 한다는 조건을 가졌기 때문
     * 하나의 상품도 없는 상태에서 상품을 등록한다면, Seller 아니라 User부터 시작.
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => ['required'],
            'description' => ['required'],
            'quantity' => ['required', 'integer', 'min:1'],
            'image' => ['required', 'image'],
        ];

        Validator::validate($request->all(), $rules);
        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = '1.jpg';
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);
    }

    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => ['integer', 'min:1'],
            'status' => [Rule::in([Product::AVAILABLE_PRODUCT, Product::UNAVAILABLE_PRODUCT])],
            'image' => ['image'],
        ];

        $this->validate($request, $rules);
        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));

        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('상품 수량이 1개 이상이어야 합니다', Response::HTTP_CONFLICT);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('상품의 수정된 내용이 없습니다.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product->save();

        return $this->showOne($product);
    }

    public function destroy(Request $request, Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        $product->delete();

        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "상품의 소유자와 판매자가 일치하지 않습니다.");
        }
    }
}
