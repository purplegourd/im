<?php
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$msg = 'hellodddddddddddddd';
$len = strlen($msg);
socket_sendto($sock, $msg, $len, 0, '127.0.0.1', 8888);
//socket_recvfrom($sock, $buf, 10, 0, $from, $port);
//echo $buf;
