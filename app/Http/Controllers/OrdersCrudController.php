<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrdersCrudController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->where('user_id', Auth::id())->latest();

        if ($request->filled('q')) {
            $q = $request->string('q')->trim()->toString();
            $query->where(function ($sub) use ($q) {
                $sub->where('item_name', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(10);

        return view('orders.index', [
            'orders'       => $orders,
            'search'       => $request->input('q', ''),
            'statusFilter' => $request->input('status', ''),
        ]);
    }

    public function create(): View
    {
        return view('orders.create');
    }

    public function store(OrderStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        Order::create($data);

        return Redirect::route('orders.index')->with('success', 'Order added successfully.');
    }

    public function edit(Order $order): View
    {
        $this->authorizeOrder($order);

        return view('orders.edit', [
            'order' => $order,
        ]);
    }

    public function update(OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $order->update($request->validated());

        return Redirect::route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $order->delete();

        return Redirect::route('orders.index')->with('success', 'Order deleted successfully.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->user_id === Auth::id(), 403);
    }
}

