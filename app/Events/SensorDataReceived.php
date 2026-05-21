<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

/**
 * Broadcast sensor data langsung ke browser via Reverb WebSocket.
 * ShouldBroadcastNow = sinkron, tidak butuh queue worker.
 * Channel publik = tidak perlu auth (monitoring dashboard).
 */
class SensorDataReceived implements ShouldBroadcastNow
{
    public function __construct(public readonly array $data) {}

    public function broadcastOn(): array
    {
        return [new Channel('sensor')];
    }

    public function broadcastAs(): string
    {
        return 'sensor.update';
    }
}
