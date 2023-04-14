<?php
/*--------------------------------------------
Date : 
Author : 
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once 'member_class.php';

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

echo session_id();