<?php

namespace Berriart\Zabbix\Maintenance\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Berriart\Zabbix\Maintenance\Service\ApiClient;

class BaseCommand extends Command
{
    protected $client;

    protected function configure()
    {
        $this
            ->addOption(
                'url',
                'U',
                InputOption::VALUE_REQUIRED,
                'URL of the Zabbix instance'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_REQUIRED,
                'User to connect to Zabbix API'
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_REQUIRED,
                'Password to connect to Zabbix API'
            )->addOption(
                'group',
                'g',
                InputOption::VALUE_REQUIRED,
                'The maintenance Zabbix group'
            )->addOption(
                'host',
                'H',
                InputOption::VALUE_OPTIONAL,
                'The server/machine host'
            )->addOption(
                'sleep',
                's',
                InputOption::VALUE_OPTIONAL,
                'Time to sleep (in seconds) after the change'
            )
        ;
    }

    protected function getIds(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        $user = $input->getOption('user');
        $pass = $input->getOption('password');
        $group = $input->getOption('group');
        $host = $input->getOption('host') ? $input->getOption('host') : gethostname();

        if (null == $url || null == $user || null == $pass || null == $group) {
            throw new InvalidOptionException('url, user, password and group options are required.');
        }

        $this->client = new ApiClient($url);

        $output->writeln('<info>Trying to connect to Zabbix....</info>');
        $auth = $this->client->authenticate($user, $pass);
        if (!$auth) {
            $output->writeln('<error>Unable to connect</error>');

            return false;
        }

        $output->writeln('<info>Getting Maintenance Mode Group...</info>');
        $zabbixgroup = $this->client->getGroupByName($group);
        if (!$zabbixgroup) {
            $output->writeln('<error>Maintenance group "' . $group . '" not found</error>');

            return false;
        }

        $output->writeln('<info>Getting server info...</info>');
        $zabbixhost = $this->client->getHost($host);
        if (!$zabbixhost) {
            $output->writeln('<error>Host "' . $host . '" not found</error>');

            return false;
        }

        return array(
            'host' => $zabbixhost->hostid,
            'group' => $zabbixgroup->groupid,
        );
    }

    protected function sleep(InputInterface $input, OutputInterface $output)
    {
        $sleep = $input->getOption('sleep') ? $input->getOption('sleep') : 0;
        if ($sleep > 0) {
            $output->writeln('<info>Sleeping ' . $sleep . ' seconds for allowing zabbix to set the host status...</info>');
            sleep($sleep);
        }
    }
}
