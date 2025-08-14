@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('notification.Notifications') }}</h2>

    {{-- Alert --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter & Search --}}
    <form method="GET" class="mb-3 d-flex flex-wrap gap-2">
        <select name="status" class="form-control" style="max-width: 200px;">
            <option value="">{{ __('notification.View All') }}</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>
                {{ __('notification.Unread') }}
            </option>
            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                {{ __('notification.Read') }}
            </option>
        </select>

        <input type="text" name="search" class="form-control" placeholder="{{ __('notification.Search') }}..."
               value="{{ request('search') }}" style="max-width: 250px;">

        <button type="submit" class="btn btn-secondary">
            {{ __('notification.Filter') }}
        </button>
    </form>

    {{-- Tombol Mark All as Read --}}
    @if ($notifications->count() > 0)
        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="mb-3">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm">
                {{ __('notification.markAllAsRead') }}
            </button>
        </form>
    @endif

    {{-- List Notifikasi --}}
    @if ($notifications->count())
        <ul class="list-group">
            @foreach ($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items-center
                    {{ is_null($notification->read_at) ? 'list-group-item-info' : '' }}">

                    {{-- Konten notifikasi --}}
                    <div>
                        @if ($notification->type === 'App\Notifications\NewUserRegistered')
                            {{ __('notification.NewUserRegistered') }}: {{ $notification->data['name'] ?? 'Unknown User' }}
                        @elseif ($notification->type === 'App\Notifications\NewProduct')
                            {{ __('notification.NewProduct') }}: {{ \App\Models\Product::find($notification->data['product_id'])->name ?? 'Unknown Product' }}
                        @elseif ($notification->type === 'App\Notifications\NewOrder')
                            {{ __('notification.NewOrder') }}: {{ __('notification.Order') }} #{{ $notification->data['order_id'] ?? 'Unknown' }}
                        @elseif ($notification->type === 'App\Notifications\OrderStatusChanged')
                            {{ __('notification.Order') }} #{{ $notification->data['order_id'] ?? 'Unknown' }}
                            {{ __('notification.StatusChangedTo') }} {{ $notification->data['status'] ?? 'Unknown Status' }}
                        @else
                            {{ $notification->data['message'] ?? __('notification.NoDetails') }}
                        @endif
                        
                        <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    {{-- Aksi --}}
                    <div class="d-flex align-items-center gap-2">
                        @if (is_null($notification->read_at))
                            <a href="{{ route('notifications.redirect', $notification->id) }}" class="btn btn-sm btn-primary">
                                {{ __('notification.View') }}
                            </a>
                        @endif

                        {{-- Tombol hapus --}}
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('{{ __('notification.ConfirmDelete') }}')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-trash"></i> {{ __('notification.Delete') }}
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $notifications->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @else
        <p>{{ __('notification.NoNotifications') }}</p>
    @endif
 
@endsection