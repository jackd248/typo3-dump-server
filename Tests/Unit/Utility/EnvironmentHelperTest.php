<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_dump_server" TYPO3 CMS extension.
 *
 * (c) 2025 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\Typo3DumpServer\Tests\Unit\Utility;

use KonradMichalik\Typo3DumpServer\Utility\EnvironmentHelper;
use PHPUnit\Framework\TestCase;

use function is_string;

/**
 * EnvironmentHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev
 * @license GPL-2.0
 */
final class EnvironmentHelperTest extends TestCase
{
    private string $originalEnvValue;

    protected function setUp(): void
    {
        $dumpServerHost = getenv('TYPO3_DUMP_SERVER_HOST');
        $this->originalEnvValue = is_string($dumpServerHost) ? $dumpServerHost : '';
    }

    protected function tearDown(): void
    {
        if ('' !== $this->originalEnvValue) {
            putenv('TYPO3_DUMP_SERVER_HOST='.$this->originalEnvValue);
        } else {
            putenv('TYPO3_DUMP_SERVER_HOST');
        }
    }

    public function testGetHostReturnsDefaultWhenEnvironmentVariableNotSet(): void
    {
        putenv('TYPO3_DUMP_SERVER_HOST');

        $host = EnvironmentHelper::getHost();

        self::assertSame('tcp://127.0.0.1:9912', $host);
    }

    public function testGetHostReturnsEnvironmentVariableWhenSet(): void
    {
        $customHost = 'tcp://192.168.1.100:9999';
        putenv('TYPO3_DUMP_SERVER_HOST='.$customHost);

        $host = EnvironmentHelper::getHost();

        self::assertSame($customHost, $host);
    }

    public function testGetHostReturnsEmptyStringWhenEnvironmentVariableIsEmpty(): void
    {
        putenv('TYPO3_DUMP_SERVER_HOST=');

        $host = EnvironmentHelper::getHost();

        self::assertSame('', $host);
    }
}
