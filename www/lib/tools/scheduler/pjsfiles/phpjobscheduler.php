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
 *   single fire, silent db connect, fire time in minutes *
 *     3.6     Oct 2009    Version check added            *
 *     3.7 Feb 2011 - DEBUG improved to aid install,
                      and new method added to ensure only one instance of the same script runs at any one time *  NOTES:                                                *
 *        Requires:  PHP and MySQL                        *
 **********************************************************/
 include_once("config.inc.php");
 include_once("constants.inc.php");
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.7";
// ---------------------------------------------------------
include_once("functions.php");
if (DEBUG) error_reporting(E_ALL);
else error_reporting(0);

db_connect();
$time_and_window =  time() + TIME_WINDOW;
$query="select * from phpjobscheduler WHERE fire_time <= $time_and_window";
$query="select * from phpjobscheduler";
$result = pjs_mysqli_query($query);
$scripts_to_run = array();
if (mysqli_num_rows($result))
{
 $i = 0;
 while ($i < mysqli_num_rows($result))
 {
  $id=mysqli_result($result,$i, 'id');
  $scriptpath=mysqli_result($result,$i, 'scriptpath');
  $time_interval=mysqli_result($result,$i, 'time_interval');
  $fire_time=mysqli_result($result,$i, 'fire_time');
  $time_last_fired=mysqli_result($result,$i, 'time_last_fired');
  $run_only_once=mysqli_result($result,$i, 'run_only_once');
  $fire_time_new = $fire_time + $time_interval;
  $scripts_to_run[$i]["script"]="$scriptpath";
  $scripts_to_run[$i]["id"]=$id;
  pjs_mysqli_query("UPDATE phpjobscheduler
                    SET
                     fire_time='$fire_time_new',
                     time_last_fired='$fire_time'
                    WHERE id='$id'");
  if($run_only_once) pjs_mysqli_query("DELETE from phpjobscheduler WHERE id='$id' ");
  $i++;
 }
}
$log_date="";
$log_output="";
db_close();
// run the scheduled scripts
for ($i = 0; $i < count($scripts_to_run); $i++) fire_script($scripts_to_run[$i]['script'],$scripts_to_run[$i]['id']);
?>
