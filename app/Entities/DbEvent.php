<?php

namespace App\Entities;

class DbEvent
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
