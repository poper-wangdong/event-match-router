<?php

namespace App\Services;

use App\Entities\DbEvent;
use App\Entities\EventConfig;

class EventBusService
{
    protected EventConfig $config;

    public function __construct(EventConfig $config)
    {
        $this->config = $config;
    }

    /**
     *
     *
     * @param DbEvent $event
     * @return bool
     */
    public function handle(DbEvent $event): bool
    {
        sleep(1);
        echo "Bus Handle...";

        return true;
    }
}
