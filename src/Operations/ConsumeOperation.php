<?php

namespace TrueIfNotFalse\LumenKafka\Operations;

use Exception;
use Junges\Kafka\Config\Sasl;
use Junges\Kafka\Consumers\ConsumerBuilder;

class ConsumeOperation
{
    /**
     * @param string $connection
     *
     * @return void
     */
    public function do(string $connection): void
    {
        $config = config('queue.connections.' . $connection);
        if (empty($config)) {
            throw new Exception('No connection found with name:' . $connection);
        }

        $brokers          = $this->getBrokers($config);
        $topics           = $this->getTopics($config);
        $groupId          = $this->getGroupId($config);
        $handler          = $this->getHandler($config);
        $securityProtocol = $this->getSecurityProtocol($config);
        $sasl             = $this->getSasl($config);

        $consumer = ConsumerBuilder::create($brokers, $topics, $groupId)
                                   ->withHandler(new $handler());

        if (! empty($securityProtocol)) {
            $consumer->withSecurityProtocol($securityProtocol);
        }

        if (! empty($sasl)) {
            $saslConfig = new Sasl($sasl['username'] ?? '', $sasl['password'] ?? '', $sasl['mechanisms'] ?? '');
            $consumer->withSasl($saslConfig);
        }

        $consumer->build()->consume();
    }

    /**
     * @param array $config
     *
     * @return string
     */
    protected function getBrokers(array $config): string
    {
        $brokers = $config['brokers'];
        if (empty($brokers)) {
            throw new Exception('Brokers not set');
        }

        return $brokers;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getTopics(array $config): array
    {
        $topics = $config['topics'] ?? [];
        if (empty($topics)) {
            throw new Exception('Topics not set');
        }

        return $topics;
    }

    /**
     * @param array $config
     *
     * @return string|null
     */
    protected function getSecurityProtocol(array $config): ?string
    {
        return $config['security_protocol'] ?? null;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getSasl(array $config): array
    {
        return $config['sasl'] ?? [];
    }

    /**
     * @param array $config
     *
     * @return string|null
     */
    protected function getGroupId(array $config): ?string
    {
        return $config['group_id'] ?? null;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    protected function getHandler(array $config): string
    {
        $handler = $config['handler'];
        if (empty($handler)) {
            throw new Exception('Handler not set');
        }

        return $handler;
    }
}
