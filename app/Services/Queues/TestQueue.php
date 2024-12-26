<?php

namespace App\Services\Queues;

use App\Contracts\QueueInterface;

class TestQueue implements QueueInterface
{

    public function pop(): array
    {
        sleep(1);

        return [
            'table' => 'users',
            'event' => 'i',
            'fields' => [
                'password',
            ],
            'values' => [
                'password' => '123456',
                'created_at' => now()
            ]
        ];
    }

    public function done(array $data)
    {
        sleep(1);
        echo "done\n";
    }

    public function back(array $data)
    {
        echo "back\n";
    }
}
