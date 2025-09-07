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

namespace Test\Sitepackage\EventListener;

use KonradMichalik\Typo3DumpServer\Event\DumpEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

#[AsEventListener]

/**
 * DemoListener.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */

class DemoListener
{
    public function __invoke(DumpEvent $event): void
    {
        $value = $event->getValue();
        $type = $event->getType();

        // DebuggerUtility::var_dump($value, $type);
    }
}
