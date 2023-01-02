<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chats';
    protected $guarded = ['id'];

    //relation
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'chat_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'chat_id')->latest('updated_at');
    }

    public function scopeHasParticipant($query,int $user_id)
    {
        return $query->whereHas('participants',function($q) use ($user_id){
            $q->where('user_id',$user_id);
        });
    }
}
