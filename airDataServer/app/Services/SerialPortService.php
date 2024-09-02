<?php

namespace App\Services;

use App\Helper\PhpSerial;
use Illuminate\Support\Facades\Log;

class SerialPortService
{
    private $serialPort;

    public function __construct()
    {
        $this->openSerialPort();
    }

    public function openSerialPort()
    {
        // 打开串口的逻辑
        Log::info('Opening serial port...');

        // 假设使用 PHP-Serial 扩展包管理串口
        $this->serialPort = new PhpSerial;
        $this->serialPort->deviceSet('COM2'); // 替换为实际的串口设备名称
        $this->serialPort->confBaudRate(9600);
        $this->serialPort->deviceOpen();

        Log::info('Serial port opened successfully.');
    }

    public function closeSerialPort()
    {
        // 关闭串口的逻辑
        if ($this->serialPort) {
            Log::info('Closing serial port...');
            $this->serialPort->deviceClose();
            Log::info('Serial port closed.');
        }
    }

    public function __destruct()
    {
        $this->closeSerialPort();
    }
}
