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

namespace KonradMichalik\Typo3DumpServer\Tests\Unit\Utility;

use KonradMichalik\Typo3DumpServer\Utility\EnvironmentHelper;
use PHPUnit\Framework\TestCase;

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
        if ($this->originalEnvValue !== '') {
            putenv('TYPO3_DUMP_SERVER_HOST=' . $this->originalEnvValue);
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
        putenv('TYPO3_DUMP_SERVER_HOST=' . $customHost);

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
