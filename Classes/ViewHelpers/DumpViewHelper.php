<?php

declare(strict_types=1);

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
