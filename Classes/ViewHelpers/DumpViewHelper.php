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

namespace KonradMichalik\Typo3DumpServer\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
* Dump ViewHelper
*
* This ViewHelper uses the Symfony VarDump to debug the content of a variable. Useful in combination with the Dump Server.
*
* Usages:
* ::
*     <html xmlns:symfony="http://typo3.org/ns/KonradMichalik/Typo3DumpServer/ViewHelpers">
*
*     <symfony:dump>{variable}</symfony:dump>
*/
class DumpViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
    }

    public function render(): string
    {
        return dump($this->renderChildren());
    }
}
