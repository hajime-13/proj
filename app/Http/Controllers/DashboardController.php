<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userCount = User::query()->count();
        $orderCount = Order::query()->count();

        // Orders by status for the logged-in user
        $myOrders = Order::query()->where('user_id', Auth::id());
        $statusCounts = [
            'pending'    => (clone $myOrders)->where('status', 'pending')->count(),
            'processing' => (clone $myOrders)->where('status', 'processing')->count(),
            'completed'  => (clone $myOrders)->where('status', 'completed')->count(),
            'cancelled'  => (clone $myOrders)->where('status', 'cancelled')->count(),
        ];

        // Recent orders for the logged-in user
        $recentOrders = Order::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'userCount'    => $userCount,
            'orderCount'   => $orderCount,
            'statusCounts' => $statusCounts,
            'recentOrders' => $recentOrders,
        ]);
    }
}

