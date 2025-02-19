<?php

declare(strict_types=1);

namespace KonradMichalik\Typo3DumpServer\Utility;

final class EnvironmentHelper
{
    private const string DEFAULT_HOST = 'tcp://127.0.0.1:9912';

    public static function getHost(): string
    {
        $host = getenv('TYPO3_DUMP_SERVER_HOST');

        if ($host === false) {
            $host = self::DEFAULT_HOST;
        }
        return $host;
    }
}
