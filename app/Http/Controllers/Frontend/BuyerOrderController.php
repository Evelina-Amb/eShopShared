<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;

class BuyerOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'orderItem.listing.user',
            'shipments'
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

        return view('frontend.buyer.orders.index', compact('orders'));
    }
}
