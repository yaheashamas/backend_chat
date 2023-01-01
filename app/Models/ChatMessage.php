<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    
    protected $table = 'chat_messages';
    protected $guarded = ['id'];

    //if change time created_at for chat that Leads to change time created_at in chart_message
    protected $touches = ['chat'];

    //relation
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chat()
    {
        return $this->belongsTo(chat::class, 'chat_id');
    }
}
