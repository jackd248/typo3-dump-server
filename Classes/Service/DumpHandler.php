<?php

declare(strict_types=1);

namespace KonradMichalik\Typo3DumpServer\Service;

use KonradMichalik\Typo3DumpServer\Utility\EnvironmentHelper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

final class DumpHandler
{
    /**
    * @see https://symfony.com/doc/current/components/var_dumper.html#the-dump-server
    */
    public static function register(): void
    {
        $cloner = new VarCloner();
        $serverAvailable = self::isServerAvailable(EnvironmentHelper::getHost());
        $suppressDumpIfServerIsUnavailable = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_dump_server']['suppressDump'] ?? false;

        if ($serverAvailable) {
            $fallbackDumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper() : new HtmlDumper();
            $dumper = new ServerDumper(EnvironmentHelper::getHost(), $fallbackDumper, [
                'cli' => new CliContextProvider(),
                'source' => new SourceContextProvider(),
            ]);

            VarDumper::setHandler(static function (mixed $var) use ($cloner, $dumper): ?string {
                return $dumper->dump($cloner->cloneVar($var));
            });
        } elseif ($suppressDumpIfServerIsUnavailable) {
            VarDumper::setHandler(function (): void {});
        }
    }

    private static function isServerAvailable(string $host): bool
    {
        $urlParts = parse_url($host);

        if (empty($urlParts['host']) || empty($urlParts['port'])) {
            return false;
        }

        $connection = @fsockopen($urlParts['host'], (int)$urlParts['port'], $errno, $errstr, 0.5);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
