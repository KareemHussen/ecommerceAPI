<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\Order\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user;
        
        return $this->respondOk($user->orders , 'Orders fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $user = $request->user;

        DB::beginTransaction();

        $result = DB::table('product_user')
        ->join('products', 'product_user.product_id', '=', 'products.id')
        ->where('product_user.user_id', $user->id)
        ->select(
            'products.id',
            'products.price',
            'products.quantity as productQuantity',
            'product_user.quantity as cartQuantity',
            DB::raw('(product_user.quantity * products.price) as total_price')
        )
        ->get();

        if(count($result) == 0) {
            return $this->respondError("Cart is empty");
        }

        $order = Order::create([
            'total' => 0,
            'user_id' => $user->id,
            'status' => 0,
        ]);

        $totalSum = 0;

        foreach($result as $row) {

            $totalSum += $row->total_price;

            if($row->cartQuantity > $row->productQuantity) {
                DB::rollBack();
                return $this->respondError("Insufficient quantity, existing quantity is ".$row->productQuantity);
            }

            // query builder to update the product quantity
            DB::table('products')->where('id', $row->id)->update(['quantity' => $row->productQuantity - $row->cartQuantity]);

            $order->products()->attach($row->id , [
                'quantity' => $row->cartQuantity,
                'price' => $row->total_price
            ]);

        }

        $totalSum = round($totalSum , 2);

        // Clear the cart after successful checkout
        $user->cart()->detach();
        
        $order->update(['total' => $totalSum]);
        

        DB::commit();

        return $this->respondOk($totalSum , "Checkout successful");

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $this->respondOk($order->load('products') , 'Order fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function completeOrder(Order $order)
    {
        if($order->status != "Pending") {
            return $this->respondError("Cannot complete an order that is not pending");
        }

        $order->update([
            'status' => "Completed",
        ]);

        return $this->respondNoContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancelOrder(Order $order)
    {
        if($order->status != "Pending") {
            return $this->respondError("Cannot cancel an order that is not pending");
        }

        $order->update([
            'status' => "Cancelled"
        ]);

        return $this->respondNoContent();
    }
}
