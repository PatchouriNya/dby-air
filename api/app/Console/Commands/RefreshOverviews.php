<?php

namespace App\Console\Commands;

use App\Models\Air\Air_detail;
use App\Models\Client\Client;
use App\Models\Client\Client_overview;
use Illuminate\Console\Command;

class RefreshOverviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:overviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新概览数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $root = Client::where('pid', 0)->first(['id'])->id;
        $this->refresh($root);
        return 1;
    }

    public function refresh($id)
    {
        $client = Client::find($id);
        if ($client->type === 1) {
            $airs = Air_detail::where('client_id', $client->id)->get();
            $air_quantity = $airs->count();
            $air_boot_quantity = $airs->where('power_state', '开机')->count();

            $temperatures = $airs->map(function ($air) {
                $temperature = $air->set_temperature;
                preg_match('/\d+/', $temperature, $matches);
                return !empty($matches) ? (int)$matches[0] : null;
            });

            $air_startup_temperature = round($temperatures->avg(), 1);
            $air_conditioning_power = $airs->sum('power');

            Client_overview::where('client_id', $client->id)->update([
                'air_quantity'            => $air_quantity,
                'air_boot_quantity'       => $air_boot_quantity,
                'air_startup_temperature' => $air_startup_temperature,
                'air_conditioning_power'  => $air_conditioning_power,
            ]);

            $this->info($client->clientname . '概览数据刷新成功');
        } else {
            // 处理目录客户的情况
            $children = Client::where('pid', $client->id)->get(['id']);
            foreach ($children as $child) {
                $this->refresh($child->id);
            }

            // 统计所有子客户的数据
            $subClientOverview = Client_overview::whereIn('client_id', $children->pluck('id'))->get();
            $totalAirQuantity = $subClientOverview->sum('air_quantity');
            $totalBootQuantity = $subClientOverview->sum('air_boot_quantity');
            $averageStartupTemperature = $subClientOverview->avg('air_startup_temperature');
            $averageConditioningPower = $subClientOverview->sum('air_conditioning_power');

            // 更新目录客户的记录
            Client_overview::where('client_id', $client->id)->update([
                'air_quantity'            => $totalAirQuantity,
                'air_boot_quantity'       => $totalBootQuantity,
                'air_startup_temperature' => $averageStartupTemperature,
                'air_conditioning_power'  => $averageConditioningPower,
            ]);

            $this->info($client->clientname . '概览数据刷新成功');
        }
    }
}
