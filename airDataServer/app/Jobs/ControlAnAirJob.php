<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ControlAnAirJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $table;
    protected $conditions;
    protected $data;

    public function __construct($table, array $conditions, array $data)
    {
        $this->table = $table;
        $this->conditions = $conditions;
        $this->data = $data;
    }

    public function handle()
    {
        DB::table($this->table)
            ->where($this->conditions)
            ->update($this->data);
    }
}

