<?php

/* * ********************************************************
 *                phpJobScheduler                         *
 *           Author:  DWalker.co.uk                       *
 *    phpJobScheduler ?Copyright 2003 DWalker.co.uk      *
 *              All rights reserved.                      *
 * *********************************************************
 *        Launch Date:  Oct 2003                          *
 *     Version    Date              Comment               *
 *     1.0       14th Oct 2003      Original release      *
 *     3.0       Nov 2005       Released under GPL/GNU    *
 *     3.0       Nov 2005       Released under GPL/GNU
 *     3.1       June 2006       Fixed modify issues,
 *                               and other minor issues
 *     3.3       Dec 2006     removed bugs/improved code
 *     3.4       Nov 2007     AJAX, and improved script
 *                       include using CURL and fsockopen
 *     3.5     Dec 2008    Improvements, including
 *   single fire, silent db connect, fire time in minutes
 *     3.6     Oct 2009    Version check added
 *     3.7 Feb 2011 - DEBUG improved to aid install,
  and new method added to ensure only one instance of the same script runs at any one time *  NOTES:                                                *
 *     modify    2012 02      날짜 시간 형식 cnstants.inc.php파일로 추출, 한글화.
 *                            로그를 자를때 자르는 위치를 지정할 수 있도록 기능 추가함.
 *
 *        Requires:  PHP and MySQL
 * ******************************************************** */
include_once("config.inc.php");
include_once("constants.inc.php");
$app_name = "phpJobScheduler";
$phpJobScheduler_version = "3.7";
// ---------------------------------------------------------
if (DBNAME == "-") {//not configured
	header("Location: ../readme.html");
	exit;
}

if (!function_exists('clean_input')) { // check to see if function is not already defined by another application

	function clean_input($string, $delete_negative = false) {
		$patterns[0] = "/'/";
		$patterns[1] = "/\"/";
		$string = preg_replace($patterns, '', $string);
		$string = trim($string);
		$string = stripslashes($string);
		$string = preg_replace("/[<>]/", '_', $string);
		if ($delete_negative and $string < 1) {
			$string = 0;
		}
		return $string;
	}

}

function update_db() {//called only when _viewing_ scheduled tasks
	db_connect();
	if (mysqli_num_rows(pjs_mysqli_query("SHOW TABLES LIKE '" . LOGS_TABLE . "'")) == 0) {
		$q_create_table = "CREATE TABLE `".LOGS_TABLE."` (
  `id` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'excute time. timestamp second.',
  `execution_time` varchar(60) NOT NULL,
  `script` varchar(128) NOT NULL,
  `output` text,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$result = pjs_mysqli_query($q_create_table);
	}
	if (mysqli_num_rows(pjs_mysqli_query("SHOW TABLES LIKE '" . PJS_TABLE . "'")) == 0) {
		$main_table = "CREATE TABLE `" . PJS_TABLE . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scriptpath` varchar(255) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `time_interval` int(11) DEFAULT NULL,
  `fire_time` int(11) NOT NULL DEFAULT '0',
  `time_last_fired` int(11) DEFAULT NULL,
  `run_only_once` tinyint(1) NOT NULL DEFAULT '0',
  `currently_running` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fire_time` (`fire_time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
		$result = pjs_mysqli_query($main_table);
	}
	$result = pjs_mysqli_query("select scriptpath from " . PJS_TABLE);
	if (mysqli_field_len($result, 0) < 255)
		$result = pjs_mysqli_query("ALTER TABLE " . PJS_TABLE . " CHANGE scriptpath scriptpath VARCHAR(255)");
	$result = pjs_mysqli_query("SHOW COLUMNS FROM " . PJS_TABLE . " LIKE 'run_only_once' ");
	if (!mysqli_num_rows($result))
		$result = pjs_mysqli_query("ALTER TABLE " . PJS_TABLE . " ADD run_only_once tinyint(1) NOT NULL DEFAULT '0'");
	$result = pjs_mysqli_query("SHOW COLUMNS FROM " . PJS_TABLE . " LIKE 'currently_running' ");
	if (!mysqli_num_rows($result))
		$result = pjs_mysqli_query("ALTER TABLE " . PJS_TABLE . " ADD `currently_running` BOOLEAN NOT NULL DEFAULT '0' AFTER `run_only_once`");
}

function pjs_mysqli_query($q) {
	return mysqli_query($q);
	if (mysqli_error() AND DEBUG)
		echo "<hr>MySQL ERROR: " . mysqli_error() . "<hr>";
}

function time_unit($time_interval) {
	global $app_name;
	$unit = array(0, 'type');
//check if its minutes
	if ($time_interval <= (59 * 60)) {
		$unit[0] = $time_interval / 60;
		$unit[1] = "<font color=\"#000000\">분</font>";
	}
//check if its hours
	if (($time_interval > (59 * 60)) AND ($time_interval <= (23 * 3600))) {
		$unit[0] = $time_interval / 3600;
		$unit[1] = "<font color=\"#ff0000\">시간</font>";
	}
// check if its days
	if (($time_interval > (23 * 3600)) AND ($time_interval <= (6 * 86400))) {
		$unit[0] = $time_interval / 86400;
		$unit[1] = "<font color=\"#FF8000\">일</font>";
	}
	if ($time_interval > (6 * 86400)) {
		$unit[0] = $time_interval / 604800;
		$unit[1] = "<font color=\"#C00000\">주</font>";
	}
	$thedomain = $_SERVER['HTTP_HOST'];
	return $unit;
}

function db_connect() {
	@$db_link = mysqli_connect(DBHOST, DBUSER, DBPASS);
	if ($db_link)
		@mysqli_select_db($db_link, DBNAME);
	if (mysqli_error() AND DEBUG)
		echo "<hr>MySQL ERROR: " . mysqli_error() . "<hr>";
	if (mysqli_error())
		exit();
	return $db_link;
}

function db_close() {
	global $db_link;
	if ($db_link)
		$result = mysqli_close($db_link);
}

function js_msg($msg) {
	echo "<script language=\"JavaScript\"><!--\n alert(\"$msg\");\n// --></script>";
}

function js_go($url = './') {
	echo "<script language=\"JavaScript\"><!--\n window.location.href='{$url}';\n// --></script>";
}

function save_log($script, $output, $execution_time) {
	$script = clean_input($script);
	$output = htmlentities($output);
	$i_query = "INSERT INTO " . LOGS_TABLE . " (script, output, execution_time) VALUES ('$script','$output','$execution_time');";
	$result = pjs_mysqli_query($i_query);
}

function show_jobs() {
	db_connect();
	$query = "select * from " . PJS_TABLE." order by name ";
	$result = pjs_mysqli_query($query);
	if (mysqli_num_rows($result)) {  // check has got some
		$i = 0;
		$table_rows = "";
		$bg_color = "#FFFFFF";
		while ($row = mysqli_fetch_object($result)) {

// $id=mysqli_result($result,$i, 'id');
			$id = $row->id;
			$scriptpath = $row->scriptpath;
			$name = $row->name;
			$time_interval = $row->time_interval;
			$fire_time = $row->fire_time;
			$time_last_fired = $row->time_last_fired;
			$currently_running = $row->currently_running == 1 ? '실행중' : '대기중';
			$run_only_once_txt = ($row->run_only_once) ? "<i><font color=\"#ff0000\"> 한번만 실행됩니다</font></i>" : "";

			$time_interval = time_unit($time_interval);
			if ($time_last_fired == 0) {
				$last_fire_date = "<font color=\"#FF8000\">실행되지 않았습니다.</font>";
			} else {
				$last_fire_date = '<a href="error-logs.php?script=' . $scriptpath . '">' . strftime(DATE_FORMAT . " <br />" . TIME_FORMAT, $time_last_fired) . '</a>';
			}
			$fire_date = strftime(DATE_FORMAT . " <br />" . TIME_FORMAT, $fire_time);
			if ($bg_color == "#E9E9E9") {
				$bg_color = "#FFFFFF"; 
			} else {
				$bg_color = "#E9E9E9";
			}
			$currently_running_bg_color=$row->currently_running==1? '#F00':$bg_color;
			$table_rows.="
<tr align=\"center\">
<th align=\"left\" bgcolor=\"$bg_color\">
<div id=\"pjs$id\">
<font color=\"#008000\">&quot;$name&quot;</font> - <a
href=\"javascript:modify($id);\">수정</a> -
<a href=\"javascript:deletepjs('" . PJS_TABLE . "',$id,'$name');\">삭제?</a> $run_only_once_txt<br>
<small>실행파일 위치: <font color=\"#000000\">$scriptpath</font></small>
</div>
</th>
<th align=\"center\" bgcolor=\"$currently_running_bg_color_color\">$currently_running</th>
<th align=\"center\" bgcolor=\"$bg_color\"><div id=\"pjs$id\">$last_fire_date</div></th>
<th align=\"center\" bgcolor=\"$bg_color\"><div id=\"pjs$id\">$fire_date</div></th>
<th align=\"center\" bgcolor=\"$bg_color\"><div id=\"pjs$id\">$time_interval[0] $time_interval[1]</div></th>
</tr>";
			$i++;
		}
	}
	else
		$table_rows = "<tr align=\"center\"><td bgcolor=\"#FFFFFF\" colspan=\"5\"><br><b><font color=\"#FF0000\">저장된 작업이 없습니다. 새로운 작업을 추가하시려면 상단 메뉴에 있는 \"신규 작업 추가\"를 이용하세요.</font></b><br><br></td></tr>";
	db_close();
	echo $table_rows;
}

function show_logs($qstart, $qscript = '') {
	db_connect();
	if (empty($qstart)) {
		$qstart = 0;
	}
	$num = 10; // logs to display
	$next_logs = $num + $qstart;
	$preg_logs = $qstart - $num;
	$query = "select * from " . LOGS_TABLE;
	if ($qscript != '') {
		$query.=" where script='{$qscript}' ";
	}
	$query.=" ORDER BY id DESC LIMIT $qstart, $num";
	$result = pjs_mysqli_query($query);
	if (mysqli_num_rows($result)) {  // check has got some
		$i = 0;
		$table_rows = "";
		$bg_color = "#FFFFFF";
		while ($i < mysqli_num_rows($result)) {
			$id = mysqli_result($result, $i, 'id');
			$script = mysqli_result($result, $i, 'script');
			$output = mysqli_result($result, $i, 'output');
			$output_decoded = html_entity_decode($output);
			$execution_time = mysqli_result($result, $i, 'execution_time');
			$log_date = strftime("Date: " . DATE_FORMAT . "  <br />Time: " . TIME_FORMAT, strtotime($id));
			$start_date = strftime("Date: " . DATE_FORMAT . "  <br />Time: " . TIME_FORMAT, (strtotime($id) - intval(preg_replace('/[^0-9.]/', '', $execution_time))));
			if ($bg_color == "#E9E9E9")
				$bg_color = "#FFFFFF"; else
				$bg_color = "#E9E9E9";
			if ($output != "")
				$show_hide = "<a href=\"javascript:show_hide('$id');\">Show/Hide</a>";
			else
				$show_hide = "결과가 없습니다.";
			$output = nl2br($output);
			$table_rows.="
<tr align=\"center\">
<th align=\"left\" bgcolor=\"$bg_color\">
<div id=\"pjs$id\">
<small>실행시간: <font color=\"#000000\">$execution_time</font>
<br>작업파일: <font color=\"#000000\">$script</font>
<br>결과<font color=\"#FF8000\">*</font>: 
$show_hide
<div id=\"$id\" style=\"display:none;background-color:#FFE6E6;color:#FF0000\">
<blockquote>$output <br></blockquote>
</div>
</small></small>
</div>
</th>
<th align=\"center\" bgcolor=\"$bg_color\">
<div id=\"pjs$id\"><small>$start_date</small></div>
</th>
<th align=\"center\" bgcolor=\"$bg_color\">
<div id=\"pjs$id\"><small>$log_date</small></div>
</th>
<th align=\"center\" bgcolor=\"$bg_color\">
<div id=\"pjs$id\"><small><a href=\"javascript:deletepjs('" . LOGS_TABLE . "',$id,'$script');\">삭제?</a></small></div>
</th>
</tr>";
			$i++;
		}
		$qend = $i + $qstart;
		db_close();
		$qstart += 1;
		echo "$table_rows </table></center></div></form> <center><strong>지금 로그는 최근 작성된 순서로 정렬하여 {$qstart}번 부터 {$qend}번 까지의 내용입니다.<br></strong>";


		$prev_link = "<strong><a href=\"error-logs.php?start=$preg_logs&script=$qscript\">&lt;&lt; 이전 로그 {$num}개 보기</a></strong> ";
		if ($qstart >= $num)
			echo $prev_link;
		$next_link = "<strong><a href=\"error-logs.php?start=$next_logs&script=$qscript\">다음 로그 {$num}개 보기 &gt;&gt;</a></strong>";
		if ($num == $i)
			echo $next_link;
		echo '<p align="center"><font color="#FF8000">* 실행결과는 <strong>' . MAX_ERROR_LOG_LENGTH . ' 글자</strong>만 보입니다. </font>더많은 내용을 확인하시려면 관리자에게 문의하세요.<br>';
	}
	else
		echo '<tr><td align="center" bgcolor="#FFFFFF" width="70%" colspan="2"><b><center><font color="#FF0000">로그가 없습니다.</font></center></b></td></tr>';
}

function fire_script($script, $id, $buffer_output = 1) {

	if ($buffer_output)
		ob_start();
	$scriptRunning = new scriptStatus;
	if ($scriptRunning->Running($id)) {
		$start_time = microtime(true);
		$fire_type = (function_exists('curl_exec') ) ? " PHP CURL " : " PHP fsockopen ";
//                 "://" satisfies both cases http:// and https://
		if (strstr($script, "://"))
			fire_remote_script($script);
		else {
//			var_dump(LOCATION . $script);exit;
			include(LOCATION . $script);
			$fire_type = " PHP include ";
		}
		if ($buffer_output)
			$output = ob_get_contents();
		else
			$output = "";
		if ($buffer_output)
			ob_end_clean();
		$execution_time = number_format((microtime(true) - $start_time), 5) . " seconds via" . $fire_type;
// truncate output to defined length
		switch (strtoupper(LOG_CUTTING_POSITION)) {
			case 'END':
				if (strlen($output) > MAX_ERROR_LOG_LENGTH) {
					$cut_start_no = strlen($output) - MAX_ERROR_LOG_LENGTH;
					if ($cut_start_no < 0) {
						$cut_start_no = 0;
					}
					$output = '... ' . substr($output, strlen($output) - MAX_ERROR_LOG_LENGTH);
				}
				break;
			case 'MIDDLE':
				if (strlen($output) > MAX_ERROR_LOG_LENGTH) {
					$cut_start_no = strlen($output) / 2 - MAX_ERROR_LOG_LENGTH / 2;
					if ($cut_start_no < 0) {
						$cut_start_no = 0;
					}
					$output = '... ' . substr($output, strlen($output) / 2 - MAX_ERROR_LOG_LENGTH / 2, MAX_ERROR_LOG_LENGTH) . '...';
				}
				break;
			default:
			case 'BEGIN':
				if (strlen($output) > MAX_ERROR_LOG_LENGTH) {
					$output = substr($output, 0, MAX_ERROR_LOG_LENGTH);
				}
				break;
		}
		if (ERROR_LOG)
			save_log($script, $output, $execution_time);
		$scriptRunning->Stopped($id);
	}
}

function fire_remote_script($url) {
	$url_parsed = parse_url($url);
	$scheme = $url_parsed["scheme"];
	$host = $url_parsed["host"];
	$port = isset($url_parsed["port"]) ? $url_parsed["port"] : 80;
	$path = isset($url_parsed["path"]) ? $url_parsed["path"] : "/";
	$query = isset($url_parsed["query"]) ? $url_parsed["query"] : "";
	$user = isset($url_parsed["user"]) ? $url_parsed["user"] : "";
	$pass = isset($url_parsed["pass"]) ? $url_parsed["pass"] : "";
	$useragent = "phpJobScheduler (http://www.dwalker.co.uk/phpjobscheduler/)";
	$referer = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	$buffer = "";
	if (function_exists('curl_exec')) {
		$ch = curl_init($scheme . "://" . $host . $path);
		curl_setopt($ch, CURLOPT_PORT, $port);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1); // true to fail silently
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $pass);
		$buffer = curl_exec($ch);
		curl_close($ch);
	} elseif ($fp = @fsockopen($host, $port, $errno, $errstr, 30)) {
		$header = "POST $path HTTP/1.0\r\nHost: $host\r\nReferer: $referer\r\n"
				. "Content-Type: application/x-www-form-urlencoded\r\n"
				. "User-Agent: $useragent\r\n"
				. "Content-Length: " . strlen($query) . "\r\n";
		if ($user != "")
			$header.= "Authorization: Basic " . base64_encode("$user:$pass") . "\r\n";
		$header.= "Connection: close\r\n\r\n";
		fputs($fp, $header);
		fputs($fp, $query);
		if ($fp)
			while (!feof($fp))
				$buffer.= fgets($fp, 8192);
		@fclose($fp);
	}
	echo $buffer;
}

function version_check() {
	global $phpJobScheduler_version;
	$version_url = "HTTP://www.dwalker.co.uk/versions/";
	echo '<script src="' . $version_url . '" type="text/javascript"></script>
<script language="JavaScript"><!--
var phpJobScheduler_version = "' . $phpJobScheduler_version . '";

var version_txt=phpJobScheduler_version;
if (LATEST_phpJobScheduler_version==phpJobScheduler_version)
{
version_txt=version_txt+"<br><font color=#008000>which is the most recent version.</font>";
}
else
{
version_txt=version_txt+"<br><b><font color=#FF0000>UPGRADE REQUIRED";
version_txt=version_txt+"<br>Please <a href=http://www.phpJobScheduler.co.uk/>visit here</a> ";
version_txt=version_txt+"to download the latest version </b>";
}
document.write(version_txt);
// --></script>';
}

class scriptStatus {

	public function Running($id) {
		$result = pjs_mysqli_query("UPDATE " . PJS_TABLE . " SET currently_running='1' where id='$id' ");
		register_shutdown_function('Clear', $id); // registed incase execution times out before Stopped called
		return $result;
	}

	public function Stopped($id) {
		$result = pjs_mysqli_query("UPDATE " . PJS_TABLE . " SET currently_running='0' where id='$id' ");
	}

}

function Clear($id) {
	db_connect();
//If things go wrong, or script timeout CLEAR script so will run next time
	$result = pjs_mysqli_query("UPDATE " . PJS_TABLE . " SET currently_running = '0' where id='$id' ");
	db_close();
}

?>
