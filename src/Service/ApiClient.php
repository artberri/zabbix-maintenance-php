<?php

namespace Berriart\Zabbix\Maintenance\Service;

use GuzzleHttp\Client;

class ApiClient
{
    const PATH = '/api_jsonrpc.php';

    private $client;
    private $auth;

    public function __construct($url)
    {
        $this->client = $client = new Client(array(
            'base_uri' => $url,
            'verify' => false
        ));
    }

    public function authenticate($user, $pass)
    {
        $requestBody = array(
            'method' => 'user.login',
            'params' => array(
                'user' => $user,
                'password' => $pass,
            ),
        );

        $response = $this->request($requestBody);

        if ($response) {
            $this->auth = $response;

            return true;
        }

        return false;
    }

    public function getGroupByName($name)
    {
        $requestBody = array(
            'method' => 'hostgroup.get',
            'auth' => $this->auth,
            'params' => array(
                'output' => 'extend',
                'selectHosts' => array('hostid', 'host'),
                'filter' => array(
                    'name' => $name,
                ),
            ),
        );

        $response = $this->request($requestBody);

        if ($response) {
            return $response[0];
        }

        return false;
    }

    public function getHost($host)
    {
        $requestBody = array(
            'method' => 'host.get',
            'auth' => $this->auth,
            'params' => array(
                'output' => 'extend',
                'selectGroups' => array('groupid', 'name'),
                'selectParentTemplates' => array('templateid', 'name'),
                'filter' => array(
                    'host' => $host,
                ),
            ),
        );

        $response = $this->request($requestBody);

        if ($response) {
            return $response[0];
        }

        return false;
    }

    public function addHostToGroup($host, $group)
    {
        $requestBody = array(
            'method' => 'hostgroup.massadd',
            'auth' => $this->auth,
            'params' => array(
                'groups' => array(
                    'groupid' => $group,
                ),
                'hosts' => array(
                    'hostid' => $host,
                ),
            ),
        );

        $response = $this->request($requestBody);

        if (isset($response->groupids) && isset($response->groupids[0]) && $group == $response->groupids[0]) {
            return true;
        }

        return false;
    }

    public function removeHostFromGroup($host, $group)
    {
        $requestBody = array(
            'method' => 'hostgroup.massremove',
            'auth' => $this->auth,
            'params' => array(
                'groupids' => array($group),
                'hostids' => array($host),
            ),
        );

        $response = $this->request($requestBody);

        if (isset($response->groupids) && isset($response->groupids[0]) && $group == $response->groupids[0]) {
            return true;
        }

        return false;
    }

    private function request($body)
    {
        $body['id'] = rand(10, 9999);
        $body['jsonrpc'] = '2.0';

        $response = $this->client->request('POST', self::PATH, array(
            'json' => $body,
        ));

        $responseContents = json_decode($response->getBody()->getContents());

        if (isset($responseContents->result)) {
            return $responseContents->result;
        }

        return null;
    }
}
