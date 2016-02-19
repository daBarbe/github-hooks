<?php

namespace GithubWebHook\Strategy;

interface StrategyInterface
{
    public function execute($payload);
}