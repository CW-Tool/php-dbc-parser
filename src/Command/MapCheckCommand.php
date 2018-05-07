<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wowstack\Dbc\Mapping;

/**
 * @codeCoverageIgnore
 */
class MapCheckCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('map:check')
            ->setDescription('Checks a map.')
            ->setHelp('This command loads a mapping file and check if it loads.')
            ;

        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the map file')
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $Map = Mapping::fromYAML($input->getArgument('file'));

        $io = new SymfonyStyle($input, $output);
        $table = new Table($output);
        $io->title('DBC Inspect');
        $io->section('Stats');
        $io->text([
            sprintf(
                'The mapping contains %u fields at %u bytes in total.',
                $Map->getFieldCount(),
                $Map->getFieldSize()
            ),
            '',
        ]);

        $io->newLine();

        $io->section('Fields');
        $table->setHeaders($Map->getFieldNames());
        $parsed_fields = $Map->getParsedFields();
        foreach ($parsed_fields as $field_name => $field_data) {
            $field_types[] = $field_data['type'];
        }
        $table->addRow($field_types);
        $table->render();
    }
}
