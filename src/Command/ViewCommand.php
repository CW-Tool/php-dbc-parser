<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
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
        /**
         * @var Table
         */
        $table = new Table($output);
        $DBC = new DBC($input->getArgument('file'), Mapping::fromYAML($input->getArgument('map')));

        $output->writeln([
            'DBC Viewer',
            '===========',
            '',
            $DBC->getPath(),
        ]);

        $output->writeln([
            '# of rows:            '.$DBC->getRecordCount(),
            '# of Bytes per row:   '.$DBC->getRecordSize(),
            '# of columns per row: '.$DBC->getFieldCount(),
        ]);

        if ($DBC->hasStrings()) {
            $string_block = $DBC->getStringBlock();
            $output->writeln([
                '# of strings:         '.count($string_block),
            ]);
        }

        $rows = $input->getOption('rows');

        $output->writeln('');
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
        $output->writeln('');
    }
}
