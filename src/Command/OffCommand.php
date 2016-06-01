<?php

namespace Berriart\Zabbix\Maintenance\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OffCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('off')
            ->setDescription('Set Zabbix Maintenance Off')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ids = $this->getIds($input, $output);
        if (!$ids) {
            return 1;
        }

        $removed = $this->client->removeHostFromGroup($ids['host'], $ids['group']);
        if (!$removed) {
            $output->writeln('<error>An error occurred while removing the host from the maintenance group</error>');

            return 1;
        }

        $output->writeln('<info>Operation successfully completed</info>');

        $this->sleep($input, $output);
    }
}
