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

namespace KonradMichalik\Typo3DumpServer\Tests\Unit\ViewHelpers;

use KonradMichalik\Typo3DumpServer\ViewHelpers\DumpViewHelper;
use PHPUnit\Framework\TestCase;

/**
 * DumpViewHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class DumpViewHelperTest extends TestCase
{
    private DumpViewHelper $viewHelper;

    protected function setUp(): void
    {
        $this->viewHelper = new DumpViewHelper();
    }

    public function testInitializeArgumentsDoesNotThrowException(): void
    {
        $this->expectNotToPerformAssertions();
        $this->viewHelper->initializeArguments();
    }

    public function testViewHelperExtendsAbstractViewHelper(): void
    {
        self::assertInstanceOf(\TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::class, $this->viewHelper);
    }
}
