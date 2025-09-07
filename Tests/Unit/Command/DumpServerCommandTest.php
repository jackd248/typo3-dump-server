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

namespace KonradMichalik\Typo3DumpServer\Tests\Unit\Command;

use KonradMichalik\Typo3DumpServer\Command\DumpServerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;


/**
 * DumpServerCommandTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */

final class DumpServerCommandTest extends TestCase
{
    private DumpServerCommand $command;

    protected function setUp(): void
    {
        $this->command = new DumpServerCommand('server:dump');
    }

    public function testCommandHasCorrectName(): void
    {
        self::assertSame('server:dump', $this->command->getName());
    }

    public function testCommandHasFormatOption(): void
    {
        $definition = $this->command->getDefinition();

        self::assertTrue($definition->hasOption('format'));
    }

    public function testInvalidFormatThrowsException(): void
    {
        $input = new ArrayInput(['--format' => 'invalid']);
        $output = new BufferedOutput();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported format "invalid".');

        $this->command->run($input, $output);
    }

    public function testValidFormatsAreAccepted(): void
    {
        $definition = $this->command->getDefinition();
        $formatOption = $definition->getOption('format');

        self::assertSame('cli', $formatOption->getDefault());
        self::assertTrue($formatOption->isValueRequired());
    }
}
