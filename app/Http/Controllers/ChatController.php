<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Http\Requests\StorCharRequest;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ChatRequest $request)
    {
        $data = $request->validated();
        $is_private = 1;
        if ($request->has('is_private')) {
            $is_private = (int)$data['is_private'];
        }

        $chats = Chat::where('is_private', $is_private)
        ->hasParticipant(auth()->user()->id)
        ->whereHas('messages')
        ->with('lastMessage.user','participants.user')
        ->latest('created_at')
        ->get();

        return $this->success($chats);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorCharRequest $request)
    {
        $data = $this->prepareStoreData($request);
        if($data['userId'] === $data['otherUserId']){
            return $this->error('You can not create a chat with your own');
        }

        $previousChat = $this->getPreviousChat($data['otherUserId']);

        if($previousChat === null){

            $chat = Chat::create($data['data']);
            $chat->participants()->createMany([
                [
                    'user_id'=>$data['userId']
                ],
                [
                    'user_id'=>$data['otherUserId']
                ]
            ]);

            $chat->refresh()->load('lastMessage.user','participants.user');
            return $this->success($chat);
        }

        return $this->success($previousChat->load('lastMessage.user','participants.user'));
    }

        /**
     * Check if user and other user has previous chat or not
     *
     * @param int $otherUserId
     * @return mixed
     */
    private function getPreviousChat(int $otherUserId) : mixed {

        $userId = auth()->user()->id;

        return Chat::where('is_private',1)
            ->whereHas('participants', function ($query) use ($userId){
                $query->where('user_id',$userId);
            })
            ->whereHas('participants', function ($query) use ($otherUserId){
                $query->where('user_id',$otherUserId);
            })
            ->first();
    }


    /**
     * Prepares data for store a chat
     *
     * @param StoreChatRequest $request
     * @return array
     */
    private function prepareStoreData(StorCharRequest $request) : array
    {
        $data = $request->validated();
        $otherUserId = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] = auth()->user()->id;

        return [
            'otherUserId' => $otherUserId,
            'userId' => auth()->user()->id,
            'data' => $data,
        ];
    }

    public function show(Chat $chat)
    {
        $chat->load('lastMessage.user','participants.user');
        return $this->success($chat);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
