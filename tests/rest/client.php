<?php

/**
 * ```sh
 * php client.php
 * ```
 */
require __DIR__ . '/../../src/Xtend/RESTful/RestClient.php';

use \Xtend\RESTful\RestClient;

// REST Client.
$client = new RestClient([
  'base_url' => 'http://localhost:9000/server.php',
  'debug' => true,
  'headers' => [
    'X-My-Key' => 'foo',
  ],
]);

// Send request with custom headers.
$res = $client->get('/');

// Output response.
echo 'HTTP Status: ' . $res->status . PHP_EOL;
echo 'HTTP Body: ' . print_r($res->response, true) . PHP_EOL;
