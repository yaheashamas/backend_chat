<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use PDO;

class ChatMessageController extends Controller
{
    public function index(ChatMessageRequest $request){
        $data = $request->validated();
        $chatID = $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = $data['page_size'] ?? 15;

        $messages = ChatMessage::where('chat_id', $chatID)
        ->with('user')
        ->latest('created_at')
        ->simplePaginate(
            $pageSize,
            ['*'],
            'page',
            $currentPage
        );

    return $this->success($messages->getCollection());
    }

    public function store(StoreMessageRequest $request){
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;

        $chatMessage = ChatMessage::create($data);
        $chatMessage->load('user');
        
        // TODO send broadcast event to pusher and send notification to onesignal services
        // $this->sendNotificationToOther($chatMessage);

        return $this->success($chatMessage,'Message has been sent successfully.');
    }

    // private function sendNotificationToOther(ChatMessage $chatMessage) : void {

        // TODO move this event broadcast to observer
    //     broadcast(new NewMessageSent($chatMessage))->toOthers();

    //     $user = auth()->user();
    //     $userId = $user->id;

    //     $chat = Chat::where('id',$chatMessage->chat_id)
    //         ->with(['participants'=>function($query) use ($userId){
    //             $query->where('user_id','!=',$userId);
    //         }])
    //         ->first();
    //     if(count($chat->participants) > 0){
    //         $otherUserId = $chat->participants[0]->user_id;

    //         $otherUser = User::where('id',$otherUserId)->first();
    //         $otherUser->sendNewMessageNotification([
    //             'messageData'=>[
    //                 'senderName'=>$user->username,
    //                 'message'=>$chatMessage->message,
    //                 'chatId'=>$chatMessage->chat_id
    //             ]
    //         ]);

    //     }

    // }
}
