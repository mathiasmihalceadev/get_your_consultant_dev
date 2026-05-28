<?php

namespace App\Support;

use Spatie\Browsershot\Browsershot;

class BrowsershotConfigurator
{
    public static function apply(Browsershot $browsershot): Browsershot
    {
        $timeout = (int) config('services.browsershot.timeout', 180);

        if ($timeout > 0) {
            $browsershot->timeout($timeout);
        }

        if (self::resolveBool(config('services.browsershot.no_sandbox', true), true)) {
            $browsershot->noSandbox();
        }

        if ($nodeBinary = config('services.browsershot.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = config('services.browsershot.npm_binary')) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($chromePath = config('services.browsershot.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        $chromiumArguments = [];

        if (self::resolveBool(config('services.browsershot.disable_dev_shm_usage', true), true)) {
            $chromiumArguments[] = 'disable-dev-shm-usage';
        }

        if (self::resolveBool(config('services.browsershot.disable_gpu', true), true)) {
            $chromiumArguments[] = 'disable-gpu';
        }

        if (self::resolveBool(config('services.browsershot.disable_setuid_sandbox', true), true)) {
            $chromiumArguments[] = 'disable-setuid-sandbox';
        }

        if (self::resolveBool(config('services.browsershot.write_options_to_file', false), false)) {
            $browsershot->writeOptionsToFile();
        }

        if ($chromiumArguments !== []) {
            $browsershot->addChromiumArguments($chromiumArguments);
        }

        return $browsershot;
    }

    private static function resolveBool(mixed $value, bool $default): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}