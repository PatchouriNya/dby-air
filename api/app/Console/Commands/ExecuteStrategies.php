<?php

namespace App\Console\Commands;

use App\Http\Controllers\SerialPort\SerialPortController;
use App\Models\Air\Air_detail;
use App\Models\Client\Air_group;
use App\Models\Client\Air_group_relationship;
use App\Models\Client\Client;
use App\Models\Strategy\Strategy;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ExecuteStrategies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:strategies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分组策略执行';

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
        $currentTime = now();
        $date = $currentTime->format('Y-m-d');
        $time = $currentTime->format('H:i');
        $week_day = $currentTime->dayOfWeek;
        $groups = Air_group::whereNotNull('strategy_id')->get();
        foreach ($groups as $group) {
            //            $strategy = Strategy::find($group->strategy_id);
            $strategy_arr = $group->strategy_id;
            // 计算策略上次执行时间
            $lastExecutedAt = strtotime($group->updated_at);
            foreach ($strategy_arr as $strategy_id) {
                $strategy = Strategy::find($strategy_id);

                $interval = $strategy->interval_time * 60; // 转换为秒
                // 判断是否需要执行策略
                if ($currentTime->timestamp - $lastExecutedAt >= $interval) {
                    if (
                        ($strategy->start_time <= $time && $strategy->end_time >= $time)
                        && ($strategy->start_date <= $date && $strategy->end_date >= $date)
                        && in_array($week_day, $strategy->week_days)) {
                        $delta = $strategy->delta;
                        $airIds = Air_group_relationship::where('group_id', $group->id)->pluck('air_id');
                        $client_id = $group->client_id;
                        if ($delta === 0) {
                            // 调用控制器方法
                            $serialPortController = resolve(SerialPortController::class);
                            // 使用 Request::create() 创建请求实例
                            $request = Request::create('/control-air-group', 'POST', [
                                'group_id'        => $group->id,
                                'wind_speed'      => $strategy->wind_speed,
                                'power_state'     => $strategy->power_state,
                                'operation_mode'  => $strategy->operation_mode,
                                'set_temperature' => strval(intval($strategy->set_temperature)),
                            ]);
                            // 调用 controlAirGroup 方法
                            $serialPortController->controlAirGroup($request);
                        } else {
                            foreach ($airIds as $airId) {
                                $air = Air_detail::find($airId);
                                if ($air->power_state === '开机') {
                                    $temperature = (int)$air->set_temperature;
                                    if (!(($temperature <= (int)$strategy->set_temperature + $delta) && ($temperature >= (int)$strategy->set_temperature - $delta))) {
                                        $temperature = (int)$strategy->set_temperature;
                                    }
                                    $serialPortController = resolve(SerialPortController::class);
                                    // 使用 Request::create() 创建请求实例
                                    $request = Request::create('/control-air', 'POST', [
                                        'air_id'          => $airId,
                                        'client_id'       => $client_id,
                                        'wind_speed'      => $strategy->wind_speed,
                                        'power_state'     => $strategy->power_state,
                                        'operation_mode'  => $strategy->operation_mode,
                                        'set_temperature' => strval($temperature),
                                    ]);
                                    // 调用 controlAirGroup 方法
                                    $serialPortController->controlAir($request);
                                }
                            }
                        }
                        $group->updated_at = now();
                        $group->save();
                        $clientname = Client::find($group->client_id)->clientname;
                        $this->info(now()->format('Y-m-d H:i:s') . ' ' . $clientname . ' 的空调组 ' . $group->name . ' 执行了策略 ' . $strategy->name);
                    }
                }
            }

        }
        return 1;
    }
}
