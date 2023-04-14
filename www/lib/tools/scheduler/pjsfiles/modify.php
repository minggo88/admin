<?php
/**********************************************************
 *                phpJobScheduler                         *
 *           Author:  DWalker.co.uk                        *
 *    phpJobScheduler ?Copyright 2003 DWalker.co.uk      *
 *              All rights reserved.                      *
 **********************************************************
 *        Launch Date:  Oct 2003                          *
 *     Version    Date              Comment               *
 *     1.0       14th Oct 2003      Original release      *
 *     3.0       Nov 2005       Released under GPL/GNU    *
 *     3.0       Nov 2005       Released under GPL/GNU    *
 *     3.1       June 2006       Fixed modify issues,     *
 *                               and other minor issues   *
 *     3.3       Dec 2006     removed bugs/improved code  *
 *     3.4       Nov 2007     AJAX, and improved script   *
 *                       include using CURL and fsockopen *
 *     3.5     Dec 2008    Improvements, including        *
 *     3.61     Dec 2009    Patched for PHP v5.3           *
 *  NOTES:                                                *
 *        Requires:  PHP and MySQL                        *
 **********************************************************/
 $phpJobScheduler_version = "3.61";
// ---------------------------------------------------------
include("functions.php");
$id=clean_input($_POST['id']);
if ( empty($id) ) {
	js_msg("작업 구분자 없이 접근하셨습니다. 목록으로 돌아 갑니다.");
	js_go();
	exit;
}
db_connect();
$query="select * from phpjobscheduler where id=$id";
$result = mysqli_query($query);
if (!$result) js_msg("There has been an error: ".mysqli_error() );
else $row = mysqli_fetch_array($result);
db_close();
// check if its hours
$interval_array = time_unit($row["time_interval"]);

//var_dump($row["time_interval"], $interval_array);
if (preg_match("/분/",$interval_array[1])>0) $minutes=$interval_array[0];
else $minutes=-1;
if (preg_match("/시간/",$interval_array[1])>0) $hours=$interval_array[0];
else $hours=-1;
if (preg_match("/일/",$interval_array[1])>0) $days=$interval_array[0];
else $days=-1;
if (preg_match("/주/",$interval_array[1])>0) $weeks=$interval_array[0];
else $weeks=-1;
include("header.html");
include("add-modify.html");
include("footer.html");
?>
<script language="JavaScript"><!--
document.getElementById('btitle').innerHTML = '작업 상세 정보';
with (document.I_F)
{
 id.value="<?php echo $row["id"]; ?>";
 name.value="<?php echo $row["name"]; ?>";
 scriptpath.value="<?php echo $row["scriptpath"]; ?>";
 minutes.value=<?php echo $minutes; ?>;
 hours.value=<?php echo $hours; ?>;
 days.value=<?php echo $days; ?>;
 weeks.value=<?php echo $weeks; ?>;
 time_last_fired.value=<?php echo $row['time_last_fired']; ?>;
 button.value="작업 수정";
 original_minutes=<?php echo $minutes; ?>;
 original_hours=<?php echo $hours; ?>;
 original_days=<?php echo $days; ?>;
 original_weeks=<?php echo $weeks; ?>;
 run_only_once.value=<?php echo $row['run_only_once']; ?>;
}
// --></script>