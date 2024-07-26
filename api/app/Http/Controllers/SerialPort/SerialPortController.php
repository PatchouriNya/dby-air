<?php

namespace App\Http\Controllers\SerialPort;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpSerial;

class SerialPortController extends Controller
{
    public function communicate(Request $request)
    {
        $serial = new PhpSerial();
        $serial->deviceSet("COM2");
        dd(123);
        // 设置串口参数
        $serial->confBaudRate(9600); // 波特率
        $serial->confParity("none"); // 校验位
        $serial->confCharacterLength(8); // 数据位
        $serial->confStopBits(1); // 停止位
        $serial->confFlowControl("none"); // 流控

        // 打开串口
        $serial->deviceOpen();

        // 要发送的十六进制数据
        $hexData = "123";
        // 将十六进制字符串转换为二进制数据
        $binaryData = hex2bin(str_replace(' ', '', $hexData));

        // 发送数据
        $serial->sendMessage($binaryData);

        // 接收数据
        sleep(1); // 等待数据传输
        $read = $serial->readPort();

        // 将接收到的二进制数据转换为十六进制字符串dssssssssd
        $receivedHexData = bin2hex($read);

        // 关闭串口
        $serial->deviceClose();

        return response()->json(['received' => $receivedHexData]);
    }
}
