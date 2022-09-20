<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'from', 'to', 'type', 'is_approved', 'comments', 'reason', 'days'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
