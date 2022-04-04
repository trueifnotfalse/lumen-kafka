# Lumen Kafka

## Installation

Install the package via composer
```bash
composer require trueifnotfalse/lumen-kafka
```

Then you should register the service provider in `bootstrap/app.php`
```php
$app->register(TrueIfNotFalse\LumenKafka\Providers\KafkaServiceProvider::class);
```

## Configuration

Add to `config/queue.php`:
```php
...
    'monitoring' => [
        'driver' => 'kafka',
        'brokers' => env('KAFKA_BROKERS', 'localhost'),
        'topics' => ['monitoring'],
        'group_id' => env('KAFKA_GROUP_ID', 'group'),
        'security_protocol' => env('KAFKA_SECURITY_PROTOCOL', 'PLAINTEXT'),
        'sasl' => [
            'username' => env('KAFKA_SASL_USERNAME'),
            'password' => env('KAFKA_SASL_PASSWORD'),
            'mechanisms' => env('KAFKA_SASL_MECHANISMS'),
        ],
        'handler' => MonitoringHandler::class,
    ],
...
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
