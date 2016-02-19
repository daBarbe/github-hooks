<?php

namespace GithubWebHook;

use GithubWebHook\Strategy\FixupStrategy;
use GithubWebHook\Strategy\StrategyInterface;
use Symfony\Component\Yaml\Yaml;

class Handler
{
    protected $config;

    public function __construct()
    {
        $this->loadConfig();

        $this->strategies = [
            'fixups' => new FixupStrategy($this->config),
        ];
    }

    protected function loadConfig()
    {
        $configFile = file_get_contents('./config/config.yml');
        $this->config = Yaml::parse($configFile);
    }

    protected function getConfigField($field)
    {
        if (!isset($this->config[$field])) {
            throw new \Exception('Cannot retrieve config field: '.$field);
        }
        return $this->config[$field];
    }

    protected function getRepository()
    {
        $this->getConfigField('repository');
    }

    protected function getToken()
    {
        $this->getConfigField('token');
    }

    public function handle($request)
    {
        if (!isset($request['payload'])) {
            return false;
        }

        $payload = json_decode($request['payload']);

        if (!$this->validate($payload)) {
            return false;
        }

        /** @var StrategyInterface $strategy */
        $strategy = null;
        switch ($this->getRequestType($payload)) {
            case 'commit':
                $strategy = $this->strategies['fixups'];
                break;
            case 'comment':
                break;
            default;
                throw new \Exception('Invalid request type');
        }

        if ($strategy) {
            $strategy->execute($payload);
        }

        return true;
    }

    protected function validate($payload)
    {
        if ($this->getRepository() && $payload->repository->full_name != $this->getRepository()) {
            throw new \Exception('Invalid repository name');
        }

        return true;
    }

    protected function getRequestType($payload)
    {
        if (!is_null($payload->ref)) {
            return 'commit';
        }

        if (!is_null($payload->comment)) {
            return 'comment';
        }
    }
}