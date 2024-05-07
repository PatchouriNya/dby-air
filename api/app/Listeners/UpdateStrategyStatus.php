<?php

namespace App\Listeners;

use App\Events\AirGroupStrategyUpdated;
use App\Models\Client\Air_group;
use App\Models\Strategy\Strategy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStrategyStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\AirGroupStrategyUpdated $event
     * @return void
     */
    public function handle(AirGroupStrategyUpdated $event)
    {
        $airGroup = $event->airGroup;
        $strategy = Strategy::find($airGroup->strategy_id);

        if ($strategy) {
            $groupExists = Air_group::where('strategy_id', $strategy->id)->exists();
            $strategy->status = $groupExists ? 1 : 0;
            $strategy->save();
        }
    }
}
