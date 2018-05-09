<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\Mapping;

/**
 * @codeCoverageIgnore
 */
class ViewCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dbc:view')
            ->setDescription('View the contents of a DBC file.')
            ->setHelp('This command allows you to view any DBC file using a file map.')
            ;

        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the DBC file')
            ->addArgument('map', InputArgument::REQUIRED, 'Path to the YAML map')
            ;

        $this
            ->addOption(
                'rows',
                null,
                InputOption::VALUE_REQUIRED,
                'Print selected number of rows',
                false
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $DBC = new DBC($input->getArgument('file'), Mapping::fromYAML($input->getArgument('map')));
        $io = new SymfonyStyle($input, $output);
        $table = new Table($output);

        $io->title('DBC Viewer');
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
        }
        $io->newLine();

        $rows = $input->getOption('rows');
        $table->setHeaders($DBC->getMap()->getFieldNames());

        foreach ($DBC as $index => $record) {
            $table->addRow($record->read());

            if (false !== $rows) {
                --$rows;
                if (0 === $rows) {
                    break;
                }
            }
        }

        $table->render();
        $io->newLine();

        $errors = $DBC->getErrors();
        if (count($errors) > 0) {
            $io->section('Errors');
            foreach ($DBC->getErrors() as $error) {
                $io->text([
                    '#'.$error['record'].' ('.$error['type'].'/'.$error['field'].'): '.$error['hint'],
                ]);
            }
            $io->newLine();
        }
    }
}
