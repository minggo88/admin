<?php
if(isset($_SERVER["SERVER_ADDR"])) {
    // 서버아이피보정
    $_SERVER["SERVER_ADDR"] = isset($_SERVER["HTTP_X_SERVER_ADDRESS"]) && $_SERVER["HTTP_X_SERVER_ADDRESS"] ? $_SERVER["HTTP_X_SERVER_ADDRESS"] : $_SERVER["SERVER_ADDR"];
    // 접속자아이피보정
    $_SERVER["REMOTE_ADDR"] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] ?  explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"])[0]: $_SERVER["SERVER_ADDR"];
}

if(! defined ('__APP_NAME__')) {
    define('__APP_NAME__', 'TRADE');
}
if(! defined ('__API_RUNMODE__')) {
    $runmode = 'live';
    if(isset($_SERVER['HTTP_HOST'])) { // run web server
        if(strpos($_SERVER['HTTP_HOST'], 'loc.')!== false) {
            $runmode = 'loc';
        }
        if(strpos($_SERVER['HTTP_HOST'], 'dev.')!== false) {
            $runmode = 'dev';
        }
        if(strpos($_SERVER['HTTP_HOST'], 'stage.')!== false) {
            $runmode = 'stage';
        }
        if(strpos($_SERVER['HTTP_HOST'], '.local')!== false) {
            $runmode = 'loc';
        }
    } else { // run cli
        // var_dump(PHP_OS); exit;
        if(strtolower(PHP_OS)=='winnt') {
            $ip = gethostbyname( gethostname() );
            // var_dump(PHP_OS, $ip); //exit;
        }
        if(strtolower(PHP_OS)=='linux') {
            exec("/sbin/ifconfig", $ips);
            $ips = implode('', $ips);
            if(strpos($ips, 'eth0')!==false) {
                exec("/sbin/ifconfig eth0 | fgrep -i inet | grep \"inet \" | awk '{print $2}'", $ip);
            }
            if(strpos($ips, 'enp0s3')!==false) { // for aws ec2
                exec("/sbin/ifconfig enp0s3 | fgrep -i inet | grep \"inet \" | awk '{print $2}'", $ip);
            }
            if(strpos($ips, 'ens5')!==false) { // for aws ec2
                exec("/sbin/ifconfig ens5 | fgrep -i inet | grep \"inet \" | awk '{print $2}'", $ip);
            }
            if(empty($ip)) {
                $ip = '127.0.0.1';
            } else {
                $ip = $ip[0];
            }
        }
        if(!$ip || $ip=='127.0.0.1' || strpos($ip, '192.168.')!==false) {
            $runmode = 'loc';
        }
        if(($ip=='127.0.0.1' || strpos($ip, '192.168.0')!==false) && isset($_SERVER['USER'])  && ($_SERVER['USER']=='ubuntu'||$_SERVER['USER']=='root')) {
            $runmode = 'dev';
        }
		if($ip=='10.10.2.157') {
			$runmode = 'stage';
		}
    }
    define('__API_RUNMODE__', $runmode);
    // var_dump(__API_RUNMODE__);exit;
}

if(! defined ('__DB_INFO__')) {
    switch(__API_RUNMODE__) {
        case 'loc' :
            $_db_info = array(
                'master' => array(
                    'host' => 'loc.master',
                    'username' => 'kkikda',
                    'password' => 'k$d^39@34',
                    'charset' => 'utf8',
                    'database' => 'kkikda2'
                ),
                'slave' => array(
                    array(
                        'host' => 'loc.slave',
                        'username' => 'kkikda',
                        'password' => 'k$d^39@34',
                        'charset' => 'utf8',
                        'database' => 'kkikda2'
                    )
                )
            );
            $_gmail_account = array(
                'smtp_username' => 'info.teaplate@gmail.com',
                'smtp_password' => 'uzhdrdcexqhqgwdj'
            );
            $_memcache_info = array(
                'host' => '127.0.0.1',
                'port' => '11211',
            );
        break;
        case 'dev' :
            $_db_info = array(
                'master' => array(
                    'host' => 'localhost',
                    'username' => 'kkikda',
                    'password' => 'k$d^39@34',
                    'charset' => 'utf8',
                    'database' => 'kkikda2'
                ),
                'slave' => array(
                    array(
                        'host' => 'localhost',
                        'username' => 'kkikda',
                        'password' => 'k$d^39@34',
                        'charset' => 'utf8',
                        'database' => 'kkikda2'
                    )
                )
            );
            $_gmail_account = array(
                'smtp_username' => 'info.teaplate@gmail.com',
                'smtp_password' => 'uzhdrdcexqhqgwdj'
            );
            $_memcache_info = array(
                'host' => '127.0.0.1',
                'port' => '11211',
            );
        break;
        case 'live' :
            $_db_info = array(
                'master' => array(
                    'host' => 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com',
                    'username' => 'kkikda',
                    'password' => 'KKe8IuK28Due82A',
                    'charset' => 'utf8',
                    'database' => 'kkikda'
                ),
                'slave' => array(
                    array(
                        'host' => 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com',
                        'username' => 'kkikda',
                        'password' => 'KKe8IuK28Due82A',
                        'charset' => 'utf8',
                        'database' => 'kkikda'
                    )
                )
            );
            $_gmail_account = array(
                'smtp_username' => 'teaplat.info@gmail.com',
                'smtp_password' => 'rwellfuyltisynch'
            );
            $_memcache_info = array(
                'host' => 'kkikdacache.a12ygy.cfg.apn2.cache.amazonaws.com',
                'port' => '11211',
            );
            define('SOCIAL_LOGIN_KAKAO_API_KEY', '');
            define('SOCIAL_LOGIN_KAKAO_REDIRECT_URI', 'https://'.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'www.kmcse.com').'/v1.0/socialLogin/redirect_uri.php?socail_type=kakao');
        break;
    }
    define('__DB_INFO__', $_db_info);
    // var_dump($_db_info); exit;
    define('__MEMCACHE_INFO__', $_memcache_info);
    define('__GOOGLE_GMAIL_USERNAME__', $_gmail_account['smtp_username']);
    define('__GOOGLE_GMAIL_PASSWORD__', $_gmail_account['smtp_password']);
}