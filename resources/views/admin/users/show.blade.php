@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('user.user detail') }}: {{ $user->name }}</h3>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('user.back to list') }}</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>{{ __('user.role') }}:</strong> {{ $user->roleData->name ?? '-' }}</p>
        </div>
    </div>

    <h5>{{ __('user.order history') }}</h5>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="orders-table">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>{{ __('user.total price') }}</th>
                        <th>Status</th>
                        <th>{{ __('user.created at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('user.no orders found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#orders-table').DataTable({
            processing: true,
            serverSide: false,
            language: {
                url: "{{ secure_asset('assets/indonesia.json') }}"
            }
        });
    });
</script>
@endpush
