<?php

namespace App\Console\Commands;

use App\Models\Air_detail;
use App\Models\Air_group;
use App\Models\Air_group_relationship;
use App\Models\Client;
use App\Models\Strategy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DataController;
class ExecuteStrategy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:strategy';

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
        $dataServer = new DataController();
        $finalRes[2] = [];
        foreach ($groups as $group) {
            //            $strategy = Strategy::find($group->strategy_id);
            $strategy_arr = json_decode($group->strategy_id,true);
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
                        && in_array($week_day, json_decode($strategy->week_days,true))) {
                        $delta = $strategy->delta;
                        $airIds = Air_group_relationship::where('group_id', $group->id)->pluck('air_id');
                        if ($delta === 0) {
                            // 先发请求看看能不能控制成功 这里的逻辑是群控 else分支的是单控
                            $res = $dataServer->controlAnAirGroup($group->id,$strategy->wind_speed,$strategy->power_state,$strategy->operation_mode,$strategy->set_temperature);
                            $res = json_decode($res->content());
                            if ($res->code === 201)
                                $finalRes[0] = '成功';
                            else
                                $finalRes[0] = '失败';
                            $finalRes[1] = $res->msg;
                        } else {
                            foreach ($airIds as $airId) {
                                $air = Air_detail::find($airId);
                                $temperature = (int)$air->set_temperature;
                                if (!(($temperature <= (int)$strategy->set_temperature + $delta) && ($temperature >= (int)$strategy->set_temperature - $delta))) {
                                    $temperature = (int)$strategy->set_temperature;
                                }
                                $res = $dataServer->controlAnAir($airId,$strategy->wind_speed,$strategy->power_state,$strategy->operation_mode,$temperature);
                                $res = json_decode($res->content());
                            }
                            if ($res->code === 201)
                                $finalRes[0] = '成功';
                            else
                                $finalRes[0] = '失败';
                            $finalRes[1] = $res->msg;
                        }
                        $group->updated_at = now();
                        $group->save();
                        $clientname = Client::find($group->client_id)->clientname;
                        Log::info(now()->format('Y-m-d H:i:s') . ' ' . $clientname . ' 的空调组 ' . $group->name . ' 执行了策略 ' . $strategy->name. ' 结果:' . $finalRes[0] . ' 原因:' . $finalRes[1]);
                    }
                }
            }

        }
        return 1;
    }

}
