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

namespace KonradMichalik\Typo3DumpServer\Command;

use KonradMichalik\Typo3DumpServer\Utility\EnvironmentHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;

/**
* @see https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/VarDumper/Command/ServerDumpCommand.php
*/
final class DumpServerCommand extends Command
{
    /** @var array<string, DumpDescriptorInterface> */
    private array $descriptors;

    /**
     * @param array<string, DumpDescriptorInterface> $descriptors
     */
    public function __construct(?string $name = null, array $descriptors = [])
    {
        $this->descriptors = $descriptors + [
            'cli' => new CliDescriptor(new CliDumper()),
            'html' => new HtmlDescriptor(new HtmlDumper()),
        ];
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, \sprintf('The output format (%s)', implode(', ', $this->getAvailableFormats())), 'cli')
            ->setHelp(
                <<<'EOF'
<info>%command.name%</info> starts a dump server that collects and displays
dumps in a single place for debugging you application:

<info>php %command.full_name%</info>

You can consult dumped data in HTML format in your browser by providing the <comment>--format=html</comment> option
and redirecting the output to a file:

<info>php %command.full_name% --format="html" > dump.html</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getOption('format');

        if (!is_string($format)) {
            throw new InvalidArgumentException('Format option must be a string.', 8369534571);
        }

        $descriptor = $this->descriptors[$format] ?? null;
        if ($descriptor === null) {
            throw new InvalidArgumentException(\sprintf('Unsupported format "%s".', $format), 8369534570);
        }

        $server = new DumpServer(EnvironmentHelper::getHost());

        $errorIo = $io->getErrorStyle();
        $errorIo->title('TYPO3 Var Dump Server');

        $server->start();

        $errorIo->success(sprintf('Server listening on %s', $server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $server->listen(function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });
        return Command::SUCCESS;
    }

    /**
     * @return array<string>
     */
    private function getAvailableFormats(): array
    {
        return array_keys($this->descriptors);
    }
}
