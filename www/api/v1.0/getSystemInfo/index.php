<?php
/**
 * 시스템 자원 정보 조회 스크립트
 *
 * cpu_percent: CPU 사용율
 * ram_unuse: RAM 사용량
 * ram_total: RAM 전체량
 * ram_percent: RAM 사용율
 * hdd_percent: HDD 사용율
 * hdd_unuse: HDD 미사용량
 * hdd_use: HDD 사용량
 * nginx_connections: Nginx 접속수
 * apache_connections: Apache 접속수
 * web_connections: 웹서버 접속수
 * mysql_connections: MySQL 접속수. 웹서버에서 MySQL로 접속한 수
 */
ob_start();
// cpu 사용량
$cpu_percent = @exec("top -b -n1 | grep -Po '[0-9.]+ id' | awk '{print 100-$1}'", $out).'%';
// ram 사용량
$ram_unuse = preg_replace('/[^0-9.]/', '', @exec("cat /proc/meminfo | grep MemAvailable")) * 1;
$ram_total = preg_replace('/[^0-9.]/', '', @exec("cat /proc/meminfo | grep MemTotal")) * 1;
$ram_percent = number_format(($ram_total - $ram_unuse) / $ram_total * 100, 2).'%';
// hdd
$hdd_percent = @exec("df -h . | grep -Po '([0-9.]+)(G|\%)'", $out);
/*$out : array(4) {
  [0]=> string(4) "47.2"  	???
  [1]=> string(4) "132G"  	Size
  [2]=> string(4) "105G"	Used
  [3]=> string(3) "28G"		Avail
  [4]=> string(3) "80%" 	Use%
}*/
// $hdd_percent = $out[4]; // hdd 사용율
$hdd_unuse = $out[3]; // hdd 남은량
$hdd_use = $out[2]; // hdd 사용량
// web connections
$nginx_connections = @exec("netstat -nap | grep :80 | wc -l") * 1;
$apache_connections = @exec("netstat -nap | grep :880 | wc -l") * 1;
$web_connections = $nginx_connections + $apache_connections;
// mysql connections
$mysql_connections = @exec("netstat -nap | grep :3306 | grep -v TIME_WAIT | wc -l") * 1;
include __dir__.('/../../lib/config.php');
$db_info = __DB_INFO__['master'];
$link_master = mysqli_connect($db_info['host'], $db_info['username'], $db_info['password'], $db_info['database']);
// mysql connections on mysql
// $mysql_connections_mysql = "show global status like 'threads_connected'"; // 미사용
ob_clean();

echo json_encode(
	array(
		'time' => time(),
		'cpu_percent' => $cpu_percent,
		'ram_unuse' => $ram_unuse,
		'ram_total' => $ram_total,
		'ram_percent' => $ram_percent,
		'hdd_percent' => $hdd_percent,
		'hdd_unuse' => $hdd_unuse,
		'hdd_use' => $hdd_use,
		'nginx_connections' => $nginx_connections,
		'apache_connections' => $apache_connections,
		'web_connections' => $web_connections,
		'mysql_connections' => $mysql_connections,
		'mysql_connect' => $link_master ? 'true' : 'false'
	)
);
