<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-5 mb-0">📊 Dashboard</h2>
    </x-slot>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 fs-4">👥</div>
                    <div>
                        <div class="text-muted small">Total Users</div>
                        <div class="fw-bold fs-4">{{ $userCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 fs-4">🛒</div>
                    <div>
                        <div class="text-muted small">Total Orders</div>
                        <div class="fw-bold fs-4">{{ $orderCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 fs-4">⏳</div>
                    <div>
                        <div class="text-muted small">Pending</div>
                        <div class="fw-bold fs-4">{{ $statusCounts['pending'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 fs-4">✅</div>
                    <div>
                        <div class="text-muted small">Completed</div>
                        <div class="fw-bold fs-4">{{ $statusCounts['completed'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Users vs Orders</div>
                <div class="card-body">
                    <canvas id="usersOrdersChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">My Orders by Status</div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">My Recent Orders</span>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->item_name }}</td>
                            <td>{{ $order->customer_name ?? '—' }}</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No orders yet. <a href="{{ route('orders.create') }}">Add one</a>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const userCount  = @json($userCount);
        const orderCount = @json($orderCount);
        const statusCounts = @json($statusCounts);

        // Bar chart: users vs orders
        new Chart(document.getElementById('usersOrdersChart'), {
            type: 'bar',
            data: {
                labels: ['Users', 'Orders'],
                datasets: [{
                    label: 'Count',
                    data: [userCount, orderCount],
                    backgroundColor: ['#3b82f6', '#22c55e'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Doughnut chart: order statuses
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        statusCounts.pending,
                        statusCounts.processing,
                        statusCounts.completed,
                        statusCounts.cancelled,
                    ],
                    backgroundColor: ['#f59e0b', '#06b6d4', '#22c55e', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</x-app-layout>
