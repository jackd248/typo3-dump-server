<?php

declare(strict_types=1);

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
    /** @var DumpDescriptorInterface[] */
    private array $descriptors = [];

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

        if (!$descriptor = $this->descriptors[$format] ?? null) {
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

    private function getAvailableFormats(): array
    {
        return array_keys($this->descriptors);
    }
}
