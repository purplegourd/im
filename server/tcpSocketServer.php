<?php
set_error_handler('error_handle', E_ALL);

function error_handle($errno, $errstr, $errfile, $errline)
{
    echo "[".microtime()."]"."#**********" . PHP_EOL . $errno . PHP_EOL . $errstr . PHP_EOL . $errfile . PHP_EOL . $errline . PHP_EOL . "*********#" . PHP_EOL;
}

$host = '127.0.0.1';
$port = '8888';
const MAX_LEN = 10;
$wirte = array();
$except = array();

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port);
socket_listen($socket);

$clients = array(
    "master" => $socket
);

while ($socket) {
    // 监听是否有连接变化
    $changed = $clients;
    $result = socket_select($changed, $write, $except, 1);
    
    // 错误处理
    if ($result === false) {
        echo socket_strerror(socket_last_error($socket));
        exit();
    }
    
    if ($result <= 0) {
        continue;
    }
    
    // 新客户端接入
    if (in_array($socket, $changed)) {
        // accept the client, and add him to the $clients array
        $clients['client_' . md5(mt_rand(100, 999))] = $newsock = socket_accept($socket);
        
        // send the client a welcome message
        socket_write($newsock, "no noobs, but ill make an exception :)\n" . "There are " . (count($clients) - 1) . " client(s) connected to the server\n");
        
        socket_getpeername($newsock, $ip);
        echo "New client connected: {$ip}\n";
        
        // remove the listening socket from the clients-with-data array
        $key = array_search($socket, $changed);
        unset($changed[$key]);
    }
    
    // loop through all the clients that have data to read from
    foreach ($changed as $read_sock) {
        // read until newline or 1024 bytes
        // socket_read while show errors when the client is disconnected, so silence the error messages
        //$data = socket_read($read_sock, MAX_LEN, PHP_NORMAL_READ);
                
        ### 后续用 socket_recv方法
        $recv = "";
        do{
            $data = socket_read($read_sock, MAX_LEN, PHP_NORMAL_READ);
            //var_dump($data);
            if($data === false){
                // remove client for $clients array
                $key = array_search($read_sock, $clients);
                unset($clients[$key]);
                echo "client disconnected.\n";
                // continue to the next client to read from, if any
                continue 2;
            }
            $recv .= $data;
            if(mb_strlen($data)<MAX_LEN){
                break;
            }
        }while($data);        
        $data = $recv;
        ###
        
        var_dump(strlen($data),$data,"{=====[" . addcslashes($data, "\r\n") . "]=====}");
        
        // check if the client is disconnected
        if ($data === false) {
            // remove client for $clients array
            $key = array_search($read_sock, $clients);
            unset($clients[$key]);
            echo "client disconnected.\n";
            // continue to the next client to read from, if any
            continue;
        }
        
        // trim off the trailing/beginning white spaces
        $data = trim($data);
        
        // check if there is any data after trimming off the spaces
        if (! empty($data)) {
            // send this to all the clients in the $clients array (except the first one, which is a listening socket)
            foreach ($clients as $key => $send_sock) {
                
                // if its the listening sock or the client that we got the message from, go to the next one in the list
                if ($send_sock == $socket || $send_sock == $read_sock)
                    continue;
                $who = array_search($read_sock, $clients);
                // write the message to the client -- add a newline character to the end of the message
                socket_write($send_sock, $who . '-said-[' . date("Y m d H:i:s") . ']' . $data . "\n");
            } // end of broadcast foreach
        }
    } // end of reading foreach
}
