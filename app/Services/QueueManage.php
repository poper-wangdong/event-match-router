<?php

namespace App\Services;

use App\Contracts\QueueInterface;
use App\Services\Queues\TestQueue;

class QueueManage
{
    public static function make(array $config): QueueInterface
    {
        return new TestQueue();
    }
}
