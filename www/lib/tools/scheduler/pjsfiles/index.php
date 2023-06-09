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
 *  NOTES:                                                *
 *        Requires:  PHP and MySQL                        *
 **********************************************************/
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.5";
// ---------------------------------------------------------
include_once("functions.php");
update_db(); // check database is up-to-date, if not add required tables
include("header.html");
if (isset($_GET['add'])) {
include("add-modify.html");
$path = realpath(dirname(__FILE__));
echo '<script type="text/javascript">document.getElementById("current_path").innerHTML="'.str_replace($_SERVER['DOCUMENT_ROOT'], '.', realpath(dirname(__FILE__)).'/').'";</script>';
} else {
include("main.html");
}
include("footer.html");
?>