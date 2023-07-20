<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Request;

class ProductCategoryController extends APiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    public function update(Request $request, Product $product, Category $category)
    {
        // 중복
        // $product->categories()->attach([$category->id]);

        // 덮어쓴다
        // $product->categories()->sync([$category->id]);

        // 중복되는게 있으면 추가하지 않고, 중복된게 없으면 덮어 쓰는 대신 추가함.
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }

    public function destroy(Request $request, Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse("Can not find", Response::HTTP_NOT_FOUND);
        }

        $product->categories()->detach([$category->id]);

        return $this->showAll($product->categories);
    }
}
