<?php

switch(__API_RUNMODE__) {
    case 'loc' :
        $db_master = array(
            'db_host' => 'localhost',
            'db_name' =>  'kkikda2',
            'db_user' =>  'kkikda',
            'db_pass' =>  'k$d^39@34',
            'db_charset' =>  'utf8'
        );
        $db_slave = $db_master;
        $_memcache_info = array(
            'host' => '127.0.0.1',
            'port' => '11211',
        );
        $_gmail_account = array(
            'smtp_username' => 'info.teaplate@gmail.com',
            'smtp_password' => 'uzhdrdcexqhqgwdj'
        );
        $_google_drive_target_folderid = '1BWHSK6ofmXWi6kUgewJlGJ-T8psue72y';
        $_google_drive_credentialsFile = 'google_drive_credentials_dev.json';
    break;
    case 'dev' :
        $db_master = array(
            'db_host' => 'localhost',
            'db_name' =>  'kkikda2',
            'db_user' =>  'kkikda',
            'db_pass' =>  'k$d^39@34',
            'db_charset' =>  'utf8'
        );
        $db_slave = $db_master;
        $_memcache_info = array(
            'host' => '127.0.0.1',
            'port' => '11211',
        );
        $_gmail_account = array(
            'smtp_username' => 'info.teaplate@gmail.com',
            'smtp_password' => 'uzhdrdcexqhqgwdj'
        );
        $_google_drive_target_folderid = '1BWHSK6ofmXWi6kUgewJlGJ-T8psue72y';
        $_google_drive_credentialsFile = 'google_drive_credentials_dev.json';
    break;
    case 'live' :
		$db_master = array(
            'db_host' => 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com',
            'db_name' =>  'kkikda',
            'db_user' =>  'kkikda',
            'db_pass' =>  'KKe8IuK28Due82A',
            'db_debug' =>  '0',
            'db_charset' =>  'utf8mb4'
		);
		$db_slave = array(
            'db_host' => 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com',
            'db_name' =>  'kkikda',
            'db_user' =>  'kkikda',
            'db_pass' =>  'KKe8IuK28Due82A',
            'db_debug' =>  '0',
            'db_charset' =>  'utf8mb4'
		);
        $_memcache_info = array(
            'host' => 'kkikdacache.a12ygy.cfg.apn2.cache.amazonaws.com',
            'port' => '11211',
        );
        $_gmail_account = array(
            'smtp_username' => 'teaplat.info@gmail.com',
            'smtp_password' => 'rwellfuyltisynch'
        );
        $_google_drive_target_folderid = '1lPFPY_0F7eG29HguJvDW3t973bARNs2F'; 
        $_google_drive_credentialsFile = 'google_drive_credentials_live.json'; 
    break;
}

$db_host = $db_master['db_host'];
$db_name = $db_master['db_name'];
$db_user = $db_master['db_user'];
$db_pass = $db_master['db_pass'];
$db_charset = $db_master['db_charset'];

function connect_db_slave() {
    global $db_master, $dbcon, $db_slave;
    if($db_master['db_host']==$db_slave['db_host']) { // 마스터와 슬레이브가 같은 머신이면 기존 마스터 연결을 그대로 사용함.
        if($db_master['db_name']!=$db_slave['db_name']) { // 단 슬레이브 database이름이 다르면 슬레이브 database를 선택해준다. 같으면 그대로 사용.
            mysqli_select_db($dbcon, $db_slave['db_name']);
        }
        return $dbcon;
    } else { // 마스터와 슬레이브가 다르면 슬레이브 정보로 다시 연결해서 db객체를 리턴한다.
        return new DB($db_slave['db_host'],$db_slave['db_name'],$db_slave['db_user'],$db_slave['db_pass'],$db_slave['db_charset']);
    }
}

define('__MEMCACHE_INFO__', $_memcache_info);
define('__GOOGLE_GMAIL_USERNAME__', $_gmail_account['smtp_username']);
define('__GOOGLE_GMAIL_PASSWORD__', $_gmail_account['smtp_password']);

// google drive
define('__GOOGLE_DRIVE_TARGET_FOLDERID__', $_google_drive_target_folderid);
define('__GOOGLE_DRIVE_CREDENTIALSFILE__', $_google_drive_credentialsFile);
