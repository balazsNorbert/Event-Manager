<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
      'user_id',
      'message',
      'is_handled',
      'sender_type'
    ];

    public function user()

    {
      return $this->belongsTo(User::class);
    }
}
