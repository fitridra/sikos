<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'tb_rooms';
    protected $primaryKey = 'room_id';

    protected $fillable = [
        'kost_id',
        'room_number',
        'status',
    ];

    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id', 'kost_id');
    }

    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Filled' : 'Available';
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'room_id', 'room_id');
    }
}
