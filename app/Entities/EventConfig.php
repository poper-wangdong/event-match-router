<?php

namespace App\Entities;

class EventConfig
{
    private array $queues = [
        'default' => [],
    ];

    private array $filters = [];

    private array $matchers = [];

    public function __construct()
    {
        $this->load();
    }

    public function load()
    {
        // 读取 yml 文件获取配置信息并保存到本地
        echo "加载配置...\n";
    }

    public function queues()
    {
        return $this->queues;
    }

    public function getQueue(string $name = null)
    {
        if (!$name || !isset($this->queues[$name])) {
            return current($this->queues);
        }

        return $this->queues[$name];
    }

    public function filters()
    {
        return $this->filters;
    }

    public function matchers()
    {
        return $this->matchers;

    }
}
