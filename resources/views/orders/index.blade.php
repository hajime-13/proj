<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="fw-semibold fs-5 mb-0">🛒 Order List</h2>
            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">+ Add Order</a>
        </div>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            {{-- Search --}}
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="text" name="q" class="form-control" placeholder="Search item or customer..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['pending','processing','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary">Search</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $i => $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $i }}</td>
                            <td>{{ $order->customer_name ?? '—' }}</td>
                            <td>{{ $order->item_name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>₱{{ number_format((float)$order->price, 2) }}</td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'pending'    => 'warning',
                                        'processing' => 'info',
                                        'completed'  => 'success',
                                        'cancelled'  => 'danger',
                                        default      => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>{{ $order->created_at?->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}"
                                          onsubmit="return confirm('Delete this order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
