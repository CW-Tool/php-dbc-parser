<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\Mapping;
use Wowstack\Dbc\Export\XMLExport;

class ExportCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dbc:export')
            ->setDescription('Dumps the contents of a DBC file.')
            ->setHelp('This command allows you to dump any DBC file using a file map.')
            ;

        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the DBC file')
            ->addArgument('map', InputArgument::REQUIRED, 'Path to the YAML map')
            ->addArgument('xml', InputArgument::REQUIRED, 'Where to create the XML dump')
            ;

        $this
            ->addOption(
                'client-version',
                null,
                InputOption::VALUE_REQUIRED,
                'WoW client version',
                '1.12.1'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $DBC = new DBC($input->getArgument('file'), Mapping::fromYAML($input->getArgument('map')));
        $XMLExport = new XMLExport();
        $XMLExport->export($DBC, $input->getArgument('xml'), $input->getOption('client-version'));
    }
}
