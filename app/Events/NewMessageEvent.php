<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
class NewMessageEvent extends Event implements ShouldBroadcast
{
public $message;
/**
* Create a new event instance.
*
* @return void
*/
public function __construct($channel, $message)
{
//
$this->message = $message;
}
public function broadcastOn()
{
return new PrivateChannel('example-channel');
}
}