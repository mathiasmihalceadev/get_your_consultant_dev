<?php

namespace App\Queue;

use Illuminate\Queue\Worker;

class CompatibilityWorker extends Worker
{
    protected function supportsAsyncSignals()
    {
        return extension_loaded('pcntl')
            && function_exists('pcntl_async_signals')
            && function_exists('pcntl_signal')
            && function_exists('pcntl_alarm');
    }
}