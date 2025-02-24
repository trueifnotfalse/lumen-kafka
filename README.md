# Lumen Kafka

## Installation

Install the package via composer
```bash
composer require trueifnotfalse/lumen-kafka
```

## Configuration

add this to `config/queue.php`:
```php
<?php

use App\Handlers\MonitoringHandler;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue supports a variety of backends via a single, unified
    | API, giving you convenient access to each backend using identical
    | syntax for each. The default queue connection is defined below.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection options for every queue backend
    | used by your application. An example configuration is provided for
    | each backend supported by Laravel. You're also free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'monitoring' => [
            'driver' => 'kafka',
            'brokers' => env('KAFKA_BROKERS', 'localhost'),
            'topics' => ['topic-name'],
            'group_id' => env('KAFKA_GROUP_ID', 'group'),
            'security_protocol' => env('KAFKA_SECURITY_PROTOCOL', 'PLAINTEXT'),
            //sals optionals
            'sasl' => [
                'username' => env('KAFKA_SASL_USERNAME'),
                'password' => env('KAFKA_SASL_PASSWORD'),
                'mechanisms' => env('KAFKA_SASL_MECHANISMS'),
            ],
            'handler' => MonitoringHandler::class,
            'auto_commit' => env('KAFKA_AUTO_COMMIT', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control how and where failed jobs are stored. Laravel ships with
    | support for storing failed jobs in a simple file or in a database.
    |
    | Supported drivers: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],
];

```

Also add this to `config/kafka.php`
```php
<?php

declare(strict_types=1);

return [
      
     | Your kafka brokers url.
     |
    'brokers' => env('KAFKA_BROKERS', 'localhost'),

    /*
     | Kafka consumers belonging to the same consumer group share a group id.
     | The consumers in a group then divides the topic partitions as fairly amongst themselves as possible by
     | establishing that each partition is only consumed by a single consumer from the group.
     | This config defines the consumer group id you want to use for your project.
     */
    'consumer_group_id' => env('KAFKA_CONSUMER_GROUP_ID', 'group'),

    'consumer_timeout_ms' => env("KAFKA_CONSUMER_DEFAULT_TIMEOUT", 2000),

    /*
     | After the consumer receives its assignment from the coordinator,
     | it must determine the initial position for each assigned partition.
     | When the group is first created, before any messages have been consumed, the position is set according to a configurable
     | offset reset policy (auto.offset.reset). Typically, consumption starts either at the earliest offset or the latest offset.
     | You can choose between "latest", "earliest" or "none".
     */
    'offset_reset' => env('KAFKA_OFFSET_RESET', 'latest'),

    /*
     | If you set enable.auto.commit (which is the default), then the consumer will automatically commit offsets periodically at the
     | interval set by auto.commit.interval.ms.
     */
    'auto_commit' => env('KAFKA_AUTO_COMMIT', true),

    'sleep_on_error' => env('KAFKA_ERROR_SLEEP', 5),

    'partition' => env('KAFKA_PARTITION', 0),

    /*
     | Kafka supports 4 compression codecs: none , gzip , lz4 and snappy
     */
    'compression' => env('KAFKA_COMPRESSION_TYPE', 'snappy'),

    /*
     | Choose if debug is enabled or not.
     */
    'debug' => env('KAFKA_DEBUG', false),

    /*
     | Repository for batching messages together
     | Implement BatchRepositoryInterface to save batches in different storage
     */
    'batch_repository' => env('KAFKA_BATCH_REPOSITORY', \Junges\Kafka\BatchRepositories\InMemoryBatchRepository::class),

    /*
     | The sleep time in milliseconds that will be used when retrying flush
     */
    'flush_retry_sleep_in_ms' => 100,

    /*
     | The cache driver that will be used
     */
    'cache_driver' => env('KAFKA_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),
];

```

Then you should register the service provider and config files in `bootstrap/app.php`
```php
$app->configure('queue');
$app->configure('kafka');

$app->register(TrueIfNotFalse\LumenKafka\Providers\KafkaServiceProvider::class);
```

### `MonitoringHandler::class`
```php
<?php

namespace App\Handlers;

use Junges\Kafka\Contracts\KafkaConsumerMessage;

class MonitoringHandler
{
    /**
     * @param KafkaConsumerMessage $message
     *
     * @return void
     */
    public function __invoke(KafkaConsumerMessage $message): void
    {
        print_r($message->getBody());
    }
}

```

## Consumer
Run
```bash
php artisan kafka:consume monitoring
```
