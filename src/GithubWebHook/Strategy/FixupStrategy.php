<?php

namespace GithubWebHook\Strategy;

use GuzzleHttp\Client;

class FixupStrategy extends AbstractStrategy
{
    public function execute($payload)
    {
        $branchName = $payload->ref;

        $client = new Client();
        $res = $client->get(
            $this->getBaseGithubApiUrl().$this->getRepository().'/commits?sha='.$branchName,
            [
                'headers' => $this->getHeaders(),
            ]);

        $commits = json_decode($res->getBody()->getContents());

        $lastCommit = reset($commits)->sha;

        $isValid = true;
        foreach($commits as $commit) {
            $message = $commit->commit->message;
            if (strstr($message, 'fixup!')) {
                $isValid = false;
                break;
            }
        }

        $client->post(
            $this->getBaseGithubApiUrl().$this->getRepository().'/statuses/'.$lastCommit,
            [
                'headers' => $this->getHeaders(),
                'body' => json_encode($this->getStatus($isValid)),
            ]
        );
    }

    protected function getStatus($isValid) {
        return [
            'state' => $isValid ? 'success' : 'error',
            'description' => $isValid ? 'Great job! No fixups here.' : 'You have fixups! Please remove them before merge.',
            'context' => 'fixup-checker',
        ];
    }


}