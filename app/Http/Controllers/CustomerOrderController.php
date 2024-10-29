<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['branch', 'items.food', 'items.extras']) // Changed from orderItems to items
            ->latest()
            ->paginate(10);

        return Inertia::render('Customer/Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load([
            'branch',
            'items.food', // Changed from orderItems to items
            'items.extras.extraOption',
        ]);

        return Inertia::render('Customer/Orders/Show', [
            'order' => $order,
            'statusTimeline' => $this->getOrderStatusTimeline($order),
        ]);
    }

    private function getOrderStatusTimeline(Order $order)
    {
        $timeline = [
            [
                'status' => 'pending',
                'label' => 'Order Placed',
                'timestamp' => $order->created_at,
                'completed' => true,
            ],
            [
                'status' => 'confirmed',
                'label' => 'Order Confirmed',
                'timestamp' => $order->confirmed_at,
                'completed' => in_array($order->status, ['confirmed', 'preparing', 'ready', 'delivering', 'delivered']),
            ],
            [
                'status' => 'preparing',
                'label' => 'Preparing',
                'timestamp' => $order->preparing_at,
                'completed' => in_array($order->status, ['preparing', 'ready', 'delivering', 'delivered']),
            ],
            [
                'status' => 'ready',
                'label' => 'Ready for Delivery',
                'timestamp' => $order->ready_at,
                'completed' => in_array($order->status, ['ready', 'delivering', 'delivered']),
            ],
            [
                'status' => 'delivering',
                'label' => 'Out for Delivery',
                'timestamp' => $order->delivering_at,
                'completed' => in_array($order->status, ['delivering', 'delivered']),
            ],
            [
                'status' => 'delivered',
                'label' => 'Delivered',
                'timestamp' => $order->delivered_at,
                'completed' => $order->status === 'delivered',
            ],
        ];

        return $timeline;
    }
}
