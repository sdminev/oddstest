<?php

namespace App\Events;

use App\Models\MatchData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MatchDataStored implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public MatchData $match;

    /**
     * Create a new event instance.
     */
    public function __construct(MatchData $match)
    {
        $this->match = $match;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('matches');
    }

    /**
     * Customize the event name if needed.
     */
    public function broadcastAs(): string
    {
        return 'MatchDataStored';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->match->id,
            'match_id' => $this->match->match_id,
            'bookmaker' => $this->match->bookmaker,
            'home_team' => $this->match->home_team,
            'away_team' => $this->match->away_team,
            'odds' => $this->match->odds,
            'created_at' => $this->match->created_at->toDateTimeString(),
        ];
    }
}