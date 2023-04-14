<?php
$runmode = 'live';

if(isset($_SERVER['HTTP_HOST'])) { // run web server
    if(strpos($_SERVER['HTTP_HOST'], 'loc.')!== false) {
        $runmode = 'loc';
    }
    if(strpos($_SERVER['HTTP_HOST'], 'dev.')!== false) {
        $runmode = 'dev';
    }
    if(strpos($_SERVER['HTTP_HOST'], '.local')!== false) {
        $runmode = 'loc';
    }
} else { // run cli
    if(strtolower(PHP_OS)=='winnt') {
        $ip = gethostbyname( gethostname() );
    }
    if(strtolower(PHP_OS)=='linux') {
        exec("/sbin/ifconfig eth0 | fgrep -i inet | cut -d : -f 2 | cut -d \" \" -f 1", $ip);
        $ip = $ip[0];
    }
    if($ip=='127.0.0.1' || strpos($ip, '192.168.0')!==false) {
        $runmode = 'loc';
    }
    if(($ip=='127.0.0.1' || strpos($ip, '192.168.0')!==false) && isset($_SERVER['user'])  && $_SERVER['user']=='ubuntu') {
        $runmode = 'dev';
    }
}

switch($runmode) {
    case 'loc' :
        $db_host = 'loc.master';
        $db_name = 'kkikda2';
        $db_user = 'kkikda';
        $db_pass = 'k$d^39@34';
        $db_charset = 'utf8';
        $db_prefix = "fusion_";
        define("DB_PREFIX", "fusion_");
    break;
    case 'dev' :
        $db_host = 'localhost';
        $db_name = 'kkikda2';
        $db_user = 'kkikda';
        $db_pass = 'k$d^39@34';
        $db_charset = 'utf8';
        $db_prefix = "fusion_";
        define("DB_PREFIX", "fusion_");
    break;
    case 'live' :
        $db_host = 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com';
        $db_name = 'kkikda';
        $db_user = 'kkikda';
        $db_pass = 'KKe8IuK28Due82A';
        $db_charset = 'utf8';
        $db_prefix = "fusion_";
        define("DB_PREFIX", "fusion_");
    break;
}
//var_dump($db_host, $runmode); exit;
