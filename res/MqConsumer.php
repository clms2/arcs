<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MqConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq:consumer {queueName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rabbit mq consumer';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $exchange = 'router';
            $queue = $this->argument('queueName');
            $config = config('rabbitmq');
            $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['pass'], $config['vhost']);
            $channel = $connection->channel();
            $message = $channel->basic_get($queue); //取出消息
            if (!empty($message)) {
                echo $message->body . ' done.';
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
            $channel->close();
            $connection->close();

        } catch (\Exception $e) {
            echo $e->getMessage() . ' line:' . $e->getLine();
        }
        
    }
}
