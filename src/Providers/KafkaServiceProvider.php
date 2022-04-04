<?php

namespace TrueIfNotFalse\LumenKafka\Providers;

use Junges\Kafka\Providers\LaravelKafkaServiceProvider;
use TrueIfNotFalse\LumenKafka\Console\Commands\ConsumeCommand;

class KafkaServiceProvider extends LaravelKafkaServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ConsumeCommand::class,
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        parent::register();
    }
}
