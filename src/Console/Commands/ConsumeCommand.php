<?php

namespace TrueIfNotFalse\LumenKafka\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;
use TrueIfNotFalse\LumenKafka\Operations\ConsumeOperation;

class ConsumeCommand extends Command
{
    protected $signature = 'kafka:consume                          
                           {connection : The name of the connection to use}';

    protected $description = 'Consume messages';

    /**
     * @var ConsumeOperation
     */
    protected ConsumeOperation $operation;

    /**
     * Create a new command instance.
     *
     * @param ConsumeOperation
     *
     * @return void
     */
    public function __construct(ConsumeOperation $operation)
    {
        parent::__construct();
        $this->operation = $operation;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            $connection = $this->argument('connection');
            $this->operation->do($connection);
        } catch (Throwable $exception) {
            Log::error('error occurred on running command ' . $this->getName(), [$exception]);
            if ((bool)env('APP_DEBUG', false)) {
                print_r($exception->getMessage());
                print_r($exception->getTraceAsString());
            }
        }
    }
}
