<?php

class connections
{

    private $host = '127.0.0.1';

    private $port = '8888';

    private $wirte = array();

    private $except = array();

    private $clients = array();
    
    private $changed = array();

    public function create()
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($socket, $this->host, $this->port);
        socket_listen($socket);
        $this->clients = array(
            "master" => $socket
        );
    }
    
    
    public function select(){
        $this->changed = $this->clients;
        $result = socket_select($changed, $this->write, $this->except, 1);
    }
}