<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_price', 
        'status', 
        'payment_type', 
        'payment_status', 
        'transaction_id', 
        'midtrans_transaction_id',
        'midtrans_response',
        'paid_at',];

    protected $casts = [
        'midtrans_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Status helpers
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed()
    {
        return in_array($this->payment_status, ['failed', 'cancelled', 'expired']);
    }
}
