<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ReportImported implements ShouldBroadcast
{
    use SerializesModels;

    public $title;
    public $message;
    public $reports;

    public function __construct($title, $message, array $reports)
    {
        $this->title = $title;
        $this->message = $message;
        $this->reports = $reports;
    }

    public function broadcastWith(): array
    {
        // This manually forces the data into the broadcast payload
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'reports' => $this->reports
        ];
    }

    public function broadcastOn()
    {
        return new Channel('team-notifications');
    }
}