<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    use HasFactory;

    protected $table = 'tb_kosts';
    protected $primaryKey = 'kost_id';

    protected $fillable = [
        'kost_name',
        'address',
        'amount',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class, 'kost_id', 'kost_id');
    }
}
