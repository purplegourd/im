<?php
set_error_handler('error_handle', E_ALL);

function error_handle($errno, $errstr, $errfile, $errline)
{
    echo "[" . microtime() . "]" . "#**********" . PHP_EOL . $errno . PHP_EOL . $errstr . PHP_EOL . $errfile . PHP_EOL . $errline . PHP_EOL . "*********#" . PHP_EOL;
}

$host = '127.0.0.1';
$port = '8888';

const MAX_LEN = 10;
$wirte = array();
$except = array();

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

socket_bind($socket, $host, $port);

while (true) {
    echo "1";
    $from = "";
    $port = 0;
    socket_recvfrom($socket, $buf, 10, 0, $from, $port);
    echo $buf;
    usleep(1000);
}
