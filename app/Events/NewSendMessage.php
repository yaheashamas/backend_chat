<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSendMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $ChatMessage;

    public function __construct(private ChatMessage $chatMessage)
    {
        $this->ChatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->ChatMessage->chat_id);
    }

    public function broadcastAn()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'chat_id' => $this->ChatMessage->chat_id,
            'message' => $this->ChatMessage->toArray(),
        ];
    }
}
