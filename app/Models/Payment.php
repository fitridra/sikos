<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'tb_payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'member_id',
        'payment_month',
        'payment_year',
        'payment_date',
        'amount',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
