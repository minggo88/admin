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
 *     modify    2012-02  $time_interval 값을 구할때 주, 일, 시간, 분을 모두 선택된 값을 더하도록 수정.
 *   single fire, silent db connect, fire time in minutes *
 *  NOTES:                                                *
 *        Requires:  PHP and MySQL                        *
 **********************************************************/
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.5";
// ---------------------------------------------------------
include("functions.php");
$id=clean_input($_POST['id']);
$name=clean_input($_POST['name']);
$scriptpath=clean_input($_POST['scriptpath']);
$minutes=clean_input($_POST['minutes'], true);
$hours=clean_input($_POST['hours'], true);
$days=clean_input($_POST['days'], true);
$weeks=clean_input($_POST['weeks'], true);
$time_last_fired=clean_input($_POST['time_last_fired']);
$fire_time=clean_input($_POST['fire_time']);
$fire_time=strtotime($fire_time);
$run_only_once=clean_input($_POST['run_only_once']);

// if ($minutes>0) $time_interval=$minutes * 60;
// elseif ($hours>0) $time_interval=$hours * 3600;
// elseif ($days>0) $time_interval=$days * 86400;
// else $time_interval=$weeks * 604800;
$time_interval = ((int) $weeks * 604800) + ((int) $days) * 86400 + ((int) $hours) * 3600 + ((int) $minutes) * 60;

if ($id>0)
{
 $time_last_fired= ($time_last_fired)? $time_last_fired:time();
 if(empty($fire_time)) {
	$fire_time = $time_last_fired + $time_interval;
 }
 $query="UPDATE phpjobscheduler
         SET
          name='$name',
          scriptpath='$scriptpath',
          time_interval='$time_interval',
          fire_time='$fire_time',
          run_only_once='$run_only_once'
         WHERE id='$id'";
}
else
{
 if(empty($fire_time)) {
  $fire_time = time() + $time_interval;
 }
 $query="INSERT INTO phpjobscheduler
          (scriptpath, name, time_interval, fire_time, time_last_fired,run_only_once)
         VALUES
          ('$scriptpath','$name','$time_interval','$fire_time','0','$run_only_once')";
}

db_connect();
$result = mysqli_query($query);
if (!$result) js_msg("There has been an error: ".mysqli_error() );
db_close();
?>
<script language="JavaScript"><!--
function moveit()
{
 url2open="index.php";
 document.location=url2open;
}
moveit();
// --></script>