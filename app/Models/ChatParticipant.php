<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;
    protected $table = 'chat_participants';
    protected $guarded = ['id'];

    //relation
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
