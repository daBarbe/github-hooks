<?php

require('vendor/autoload.php');

function getStatus($isValid) {
    return [
        'state' => $isValid ? 'success' : 'error',
        'description' => $isValid ? 'Great job!' : 'You have fixups!',
        'context' => 'fixup-checker',
    ];
}

if (!file_exists('config.yml')) {
    throw new \Exception('Missing configuration file');
}

$config = Symfony\Component\Yaml\Yaml::parse(file_get_contents('config.yml'));
$repository = $config['repository'];
$token = $config['token'];

// Check for the GitHub WebHook Payload
if (!isset($_POST['payload'])) {
    throw new \Exception('Fixup-WebHook-Error: missing expected POST parameter [payload]');
}

$payload = json_decode($_POST['payload']);

if ($repository && $payload->repository->full_name != $repository) {
    throw new \Exception('Fixup-WebHook-Error: invalid repository');
}

$branchName = $payload->ref;

if (empty($branchName)) {
    return;
}

$client = new GuzzleHttp\Client();
$res = $client->get(
    'https://api.github.com/repos/'.$repository.'/commits?sha='.$branchName,
    [
        'headers' => ['Authorization' => 'token '.$token],
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
    'https://api.github.com/repos/'.$repository.'/statuses/'.$lastCommit,
    [
        'headers' => ['Authorization' => 'token '.$token],
        'body' => json_encode(getStatus($isValid)),
    ]
);



