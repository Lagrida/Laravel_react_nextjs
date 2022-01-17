<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Cache::remember('products', now()->addMinutes(30), fn() => Product::all());
        return $products;
    }
    
    public function backend(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $sortPrice = $request->input('sort_price');
        $count = 15;
        $products = Cache::remember('products', now()->addMinutes(30), fn() => Product::all());
        if($search != null){
            $products = $products->filter(fn(Product $product) => Str::contains($product->title, $search) || Str::contains($product->description, $search));
        }
        if($sortPrice != null && in_array($sortPrice, ['asc', 'desc'])){
            $products = $products->sortBy([
                ['price', $sortPrice]
            ]);
        }
        $total = $products->count();
        $last_page = ceil($total/$count);
        return response([
            'data' => $products->forPage($page, $count)->values(),
            'infos' => [
                'page' => $page,
                'total' => $total,
                'last_page' => ($last_page ? $last_page : 1)
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create($request->only(['title', 'description', 'image', 'price']));
        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response($product, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->only(['title', 'description', 'image', 'price']));
        return response($product, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
    }
}
