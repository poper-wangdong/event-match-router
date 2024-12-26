<?php

namespace App\Console\Commands;

use App\Contracts\QueueInterface;
use App\Entities\DbEvent;
use App\Entities\EventConfig;
use App\Services\EventBusService;
use App\Services\QueueManage;
use Illuminate\Console\Command;

class EventRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-router';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '消费队列内容并在过滤后分发到对应的事件处理器';

    protected string $fromQueueName = 'default';
    protected EventConfig $config;

    protected QueueInterface $queue;

    /**
     * 消费事件的来源
     *
     * @var EventBusService
     */
    protected EventBusService $service;

    private bool $run =true;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 初始化项目
        $this->init();

        // 注册信号处理重载配置与主动退出
        $this->regSignal();

        // 读取上游消息
        while ($data = $this->queue->pop()) {
            echo "Data...";
            sleep(1);
            $event = new DbEvent($data);

            // 处理完成就标记为结束
            if($this->service->handle($event)) {
                $this->queue->done($data);
            } else {
                $this->queue->back($data);
            }

            // 处理一次系统信号
//            $this->signalDispatch();

            // 是否需要结束
            if (!$this->run) {
                break;
            }
        }

        echo "结束\n";



        /**
         * 实现核心逻辑
         *
         * 从哪里获取事件,只关注一个事件队列来源,如果有指定了多个,则需要参数中指定监听哪个队列
         * 来源队列不会热更新配置,只能通过重启来实现
         * 监听pcntl信号,当收到SIGUSR1信号时,重新加载配置,或处理退出任务
         * 从队列中获取事件,包装成事件对象
         * 将事件对象交给过滤器,过滤器会根据配置过滤字段内容(过滤分为 empty 字段置空 和 丢弃该事件
         * 过滤后的事件进入匹配器,先检查表名是否相同,然后检查是否有修改字段, 该模型只返回 bool 类型
         * 如果匹配到事件处理器,则将事件按照配置推送到对应的消息队列
         * 将处理完成的任务标记为已完成
         *
         * 每处理完一个任务 就给 pcntl_signal_dispatch() 一个机会
         * 如果发现有新的配置,则重新加载配置
         * 处理中的任务会放入专门的队列,等真的处理完成后再移除(如果来源支持标记任务完成则直接利用该机制)
         */




        // 主动获取一次配置到本地

        // 获取需要连接到的消费队列

        // 连接到消费队列

        // 循环消费队列内容

        // 解析队列内容

        // 先送到过滤器清洗数据

        // 尝试匹配到对应的事件处理器

        // 如果匹配到则分发到事件处理器

        // 将任务标记为已完成

        // 释放资源
    }

    public function init(): void
    {
        // 初始化事件处理对象
        if($this->hasOption('from')) {
            $this->fromQueueName = $this->option('from');
        }

        // 先加载配置
        $this->config = new EventConfig();

        // 初始化项目
        $this->service = new EventBusService($this->config);

        // 获取来源队列
        $queue_config = $this->config->getQueue($this->fromQueueName);
        $this->queue = QueueManage::make($queue_config);
    }

    public function regSignal(): void
    {
        // 先注册 pcntl 信号处理器 只关心配置更新信号
        if (!function_exists('pcntl_signal')) {
            $this->warn("需要 pcntl 支持");
        }

        // 重载配置
        pcntl_signal(SIGUSR1, [$this->config, 'load']);

        // 主动退出
        pcntl_signal(SIGQUIT, [$this, 'setQuit']);
        pcntl_signal(SIGTERM, [$this, 'setQuit']);
        pcntl_signal(SIGINT, [$this, 'setQuit']);
    }

    public function setQuit(): void
    {
        echo "收到结束通知";
        $this->run = false;
    }

    private function signalDispatch(): void
    {
        pcntl_signal_dispatch();
    }
}
