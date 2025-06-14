<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'tb_members';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'full_name',
        'address',
        'phone',
        'room_id',
        'move_in_date',
        'move_out_date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_id', 'member_id');
    }
}
