<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "typo3_dump_server".
 *
 * Copyright (C) 2025 Konrad Michalik <hej@konradmichalik.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace KonradMichalik\Typo3DumpServer\Service;

use KonradMichalik\Typo3DumpServer\Event\DumpEvent;
use KonradMichalik\Typo3DumpServer\Utility\EnvironmentHelper;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class DumpHandler
{
    private const SERVER_CONNECTION_TIMEOUT = 0.5;

    private static ?EventDispatcherInterface $eventDispatcher = null;

    /**
    * @see https://symfony.com/doc/current/components/var_dumper.html#the-dump-server
    */
    public static function register(): void
    {
        $cloner = new VarCloner();
        $serverAvailable = self::isServerAvailable(EnvironmentHelper::getHost());
        $suppressDumpIfServerIsUnavailable = false;
        if (
            isset($GLOBALS['TYPO3_CONF_VARS']) &&
            is_array($GLOBALS['TYPO3_CONF_VARS']) &&
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']) &&
            is_array($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']) &&
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_dump_server']) &&
            is_array($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_dump_server']) &&
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_dump_server']['suppressDump'])
        ) {
            $suppressDumpIfServerIsUnavailable = (bool)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_dump_server']['suppressDump'];
        }

        if ($serverAvailable) {
            $fallbackDumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) ? new CliDumper() : new HtmlDumper();
            $dumper = new ServerDumper(EnvironmentHelper::getHost(), $fallbackDumper, [
                'cli' => new CliContextProvider(),
                'source' => new SourceContextProvider(),
            ]);

            VarDumper::setHandler(static function (mixed $var) use ($cloner, $dumper): ?string {
                $data = $cloner->cloneVar($var);
                $context = [];

                // Dispatch PSR-14 event with original variable
                $eventDispatcher = self::getEventDispatcher();
                if ($eventDispatcher !== null) {
                    $event = new DumpEvent($var, $context);
                    $eventDispatcher->dispatch($event);
                }

                // Process the dump
                return $dumper->dump($data);
            });
        } elseif ($suppressDumpIfServerIsUnavailable) {
            VarDumper::setHandler(function (): void {});
        }
    }

    private static function isServerAvailable(string $host): bool
    {
        $urlParts = parse_url($host);

        if ($urlParts === false || !isset($urlParts['host'], $urlParts['port'])) {
            return false;
        }

        if ($urlParts['host'] === '' || $urlParts['port'] === 0) {
            return false;
        }

        $connection = @fsockopen(
            $urlParts['host'],
            $urlParts['port'],
            $errno,
            $errstr,
            self::SERVER_CONNECTION_TIMEOUT
        );

        if ($connection !== false) {
            fclose($connection);
            return true;
        }

        return false;
    }

    /**
     * Get the TYPO3 event dispatcher instance.
     */
    private static function getEventDispatcher(): ?EventDispatcherInterface
    {
        if (self::$eventDispatcher === null) {
            try {
                // Get TYPO3 PSR-14 event dispatcher
                self::$eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);
            } catch (\Throwable $e) {
                // Event dispatcher not available (e.g., during bootstrap)
                self::$eventDispatcher = null;
            }
        }

        return self::$eventDispatcher;
    }
}
