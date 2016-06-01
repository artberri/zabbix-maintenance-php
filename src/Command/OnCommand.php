<?php

namespace Berriart\Zabbix\Maintenance\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OnCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('on')
            ->setDescription('Set Zabbix Maintenance On')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ids = $this->getIds($input, $output);
        if (!$ids) {
            return 1;
        }

        $added = $this->client->addHostToGroup($ids['host'], $ids['group']);
        if (!$added) {
            $output->writeln('<error>An error occurred while adding the host to the maintenance group</error>');

            return 1;
        }

        $output->writeln('<info>Operation successfully completed</info>');

        $this->sleep($input, $output);
    }
}
