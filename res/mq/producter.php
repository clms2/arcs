<?php
    public function publishMqMsg($msg, $queue = 'test', $exchange = 'router')
    {
        $config = config('rabbitmq');
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['pass'], $config['vhost']);
        $channel = $connection->channel(); 
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange); // 队列和交换器绑定
        $message = new AMQPMessage($msg, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange); // 推送消息
        $channel->close();
        $connection->close();
    }
