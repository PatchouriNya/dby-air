<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client\Air_group as AirGroup;

class AirGroupStrategyDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $airGroup;

    public function __construct(AirGroup $airGroup)
    {
        $this->airGroup = $airGroup;
    }
}
