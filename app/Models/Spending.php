<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spending extends Model
{
    use HasFactory;

    protected $table = 'tb_spending';
    protected $primaryKey = 'spending_id';

    protected $fillable = [
        'kost_id',
        'spending_name',
        'spending_date',
        'amount',
    ];

    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id', 'kost_id');
    }
}
