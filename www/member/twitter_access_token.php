<?php
// Access token 을 포함한 TwitterOauth Object 생성
session_start();

// library 로드, 변수 설정 등
require_once("../lib/twitter/twitteroauth.php");
$consumer_key = "";
$consumer_secret = "";

// Access token 을 포함한 TwitterOAuth object 생성
$connection = new TwitterOAuth($consumer_key, $consumer_secret, "", "");

// get user profile
$user = $connection->get("account/verify_credentials");

echo "<pre>";
print_r($user);
echo "</pre>";
?>