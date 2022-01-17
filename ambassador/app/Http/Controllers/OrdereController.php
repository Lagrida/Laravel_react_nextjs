<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdereRequest;
use App\Http\Resources\OrdereResource;
use App\Models\Link;
use App\Models\Ordere;
use App\Models\OrderItem;
use App\Models\Product;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrdereController extends Controller
{
    public function index()
    {
        //return OrdereResource::collection(Ordere::all());
        return OrdereResource::collection(Ordere::with('orderItems')->get());
    }
    public function store(OrdereRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->validated();
            $productsInput = collect($request->input('products'));
            $link = Link::where('code', $data['link_code'])->firstOrFail();
            $linkUser = $link->user;
            $data['link_id'] = $link->id;
            $data['user_id'] = $linkUser->id;
            $data['link_ambassador_email'] = $linkUser->email;
            $ordere = Ordere::create($data);
            //$productIds = $productsInput->map(fn($product) => $product['product_id']);
            $productIds = $productsInput->pluck('product_id');
            $product = Product::findOrFail($productIds);
            $orderItems = collect([]);
            $lineItems = collect([]);
            $product->each(function(Product $product) use ($orderItems, $productsInput, $ordere, $lineItems){
                $productInput = $productsInput->firstWhere('product_id', $product->id);
                $orderItem = new OrderItem();
                $orderItem->product_title = $product->title;
                $orderItem->product_price = $product->price;
                $orderItem->quantity = $productInput['quantity'];
                $orderItem->admin_revenue = 0.9 * $product->price * $productInput['quantity'];
                $orderItem->ambassador_revenue = 0.1 * $product->price * $productInput['quantity'];

                $orderItem->product_id = $product->id;
                $orderItem->ordere_id = $ordere->id;

                $orderItems->push($orderItem);
                $lineItems->push([
                    'name' => $product->title,
                    'description' => $product->description,
                    'images' => [
                        $product->images
                    ],
                    'amount' => 100 * $product->price,
                    'currency' => 'usd',
                    'quantity' => $productInput['quantity']
                ]);
            });
            $ordere->orderItems()->createMany($orderItems->toArray());

            $stripe = Stripe::make(env('STRIPE_SECRET'));

            $source = $stripe->checkout()->sessions()->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems->toArray(),
                'success_url' => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SUCCESS_ID}',
                'cancel_url' => env('CHECKOUT_URL') . '/cancel',
            ]);

            $ordere->transaction_id = $source['id'];

            $ordere->save();
            DB::commit();
            //return response($ordere->load('orderItems'), Response::HTTP_CREATED);
            return response($source, Response::HTTP_CREATED);
        }catch(Throwable $e){
            DB::rollBack();
            return response([
                'message' => 'Error Data Base',
                'description' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function confirm(Request $request)
    {
        $transaction_id = $request->input('transaction_id', '');
        $ordere = Ordere::where('transaction_id', $transaction_id)->where('is_completed', false)->firstOrFail();
        $ordere->is_completed = true;

        $ordere->save();

        return response([
            'message' => 'Success'
        ], Response::HTTP_OK);
    }
}
