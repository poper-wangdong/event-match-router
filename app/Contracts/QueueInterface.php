<?php

namespace App\Contracts;

interface QueueInterface
{

    public function pop(): array;

    public function done(array $data);

    public function back(array $data);
}
