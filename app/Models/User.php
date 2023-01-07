<?php

namespace App\Models;

use App\Notifications\SendNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    //create userToken
    const USER_TOKEN = 'userToken';

    //relations
    public function chats()
    {
        return $this->hasMany(Chat::class,'created_by');
    }
    
    public function routeNotificationForOneSignal() : array{
        return ['tags'=>['key'=>'userId','relation'=>'=', 'value'=>(string)($this->id)]];
    }

    public function sendNewMessageNotification(Array $array){
        $this->notify(new SendNotification($array));
    }
}
