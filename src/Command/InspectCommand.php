<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wowstack\Dbc\DBC;

/**
 * @codeCoverageIgnore
 */
class InspectCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dbc:inspect')
            ->setDescription('Inspect a DBC file.')
            ->setHelp('This command allows you to inspect any DBC file and receive basic information about it.')
            ;

        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the DBC file')
            ;

        $this
            ->addOption(
                'string-samples',
                null,
                InputOption::VALUE_REQUIRED,
                'Print selected number of string samples',
                10
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $DBC = new DBC($input->getArgument('file'));

        $io = new SymfonyStyle($input, $output);
        $io->title('DBC Inspect');
        $io->section('Stats');
        $io->text([
            sprintf(
                'The %s file contains %u rows at %u bytes per column, split into %u fields.',
                $DBC->getName(),
                $DBC->getRecordCount(),
                $DBC->getRecordSize(),
                $DBC->getFieldCount()
            ),
            '',
        ]);

        if ($DBC->hasStrings()) {
            $string_block = $DBC->getStringBlock();
            $io->section('Strings');
            $io->text([
                sprintf(
                    'The %s file contains %u strings.',
                    $DBC->getName(),
                    count($string_block)
                ),
                '',
            ]);

            $string_samples = $input->getOption('string-samples');

            foreach ($string_block as $index => $string) {
                $io->text([$index.': '.$string]);
                --$string_samples;
                if (0 === $string_samples) {
                    break;
                }
            }
        }
        $io->newLine();
    }
}
