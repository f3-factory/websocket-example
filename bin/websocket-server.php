#!/usr/bin/env php
<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

// Prohibit direct HTTP access (for the sake of completeness,
// as this file is not publicly available).
if (PHP_SAPI != 'cli') {
    header('HTTP/1.1 404 Not Found');
    exit(1);
}

if ($argc != 2) {
    $message = [
        'ERROR: Expected one argument, e.g. tcp://127.0.0.1:9001',
        '',
        '> ' . $argv[0] . ' tcp://127.0.0.1:9001'
    ];

    fwrite(STDERR, implode("\n", $message) . "\n\n");
    exit(1);
}

$listen_on_socket = $argv[1];

ini_set('default_socket_timeout', 3);

/**
 * Initialize the framework and configure the WebSocket server.
 */
$f3 = Base::instance();
$f3->AUTOLOAD .= ';' . dirname(__DIR__) . '/src/';
$f3->DEBUG = 2;

// Overwrite Fat-Free Framework's default error reporting level.
error_reporting(
    (E_ALL | E_STRICT) & ~(E_NOTICE | E_USER_NOTICE | E_WARNING | E_USER_WARNING)
);

$f3->ONERROR = function (Base $f3) {
    echo $f3->get('ERROR.text') . "\n";

    foreach (explode("\n", trim($f3->get('ERROR.trace'))) as $line) {
        echo $line . "\n";
    }
};

// 1. Instantiate the WebSocket implementation without encryption.
//    Consult fatfreeframework.com for more details.
$ws = new CLI\WS($listen_on_socket);

// 2. Configure the WebSocket implementation.
new App\WebSocketServer($ws);

// 3. Start the server.
$ws->run();
