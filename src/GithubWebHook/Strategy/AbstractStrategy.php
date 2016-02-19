<?php

namespace GithubWebHook\Strategy;


abstract class AbstractStrategy implements StrategyInterface
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
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
        return $this->getConfigField('repository');
    }

    protected function getToken()
    {
        return $this->getConfigField('token');
    }

    protected function getBaseGithubApiUrl()
    {
        return 'https://api.github.com/repos/';
    }

    protected function getHeaders()
    {
        return [
            'Authorization' => 'token '.$this->getToken(),
        ];
    }

}