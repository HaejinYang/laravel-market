<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::has('products')->get();

        return response()->json(['data' => $sellers], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seller = Seller::has('products')->findOrFail($id);

        return response()->json(['data' => $seller], Response::HTTP_OK);
    }
}
