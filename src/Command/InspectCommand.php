<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

        $output->writeln([
            'DBC Inspect',
            '===========',
            '',
            $DBC->getPath(),
            '',
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
                '',
            ]);

            $string_samples = $input->getOption('string-samples');

            $output->writeln([
                'String samples',
                '--------------',
                '',
            ]);

            foreach ($string_block as $index => $string) {
                $output->writeln([$index.': '.$string]);
                --$string_samples;
                if (0 === $string_samples) {
                    break;
                }
            }
        }
        $output->writeln('');
    }
}
