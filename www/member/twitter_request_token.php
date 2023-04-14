<?php
session_start();

// library 로드, 변수 설정 등
require_once("../lib/twitter/twitteroauth.php");
$consumer_key = "";
$consumer_secret = "";

// TwitterOAuth object 생성
$connection = new TwitterOAuth($consumer_key, $consumer_secret);

// request token 발급
$request_token = $connection->getRequestToken();

// 지난 소스와 달리 직접 콜백될 URL을 직접 지정해 주었다.
//$domain = "https://" . $_SERVER['HTTP_HOST'] . "/";
//$request_token = $connection->getRequestToken($domain . "member/twitter_access_token.php");

// request token은 사용자 인증이 보내질 페이지다. 아래와 같은 방식으로 기술하여서도 해결이 가능하다.
// $domain = "http://" . $_SERVER['HTTP_HOST'] . "/";
// $request_token = $connection->getRequestToken($domain . "wicked_home/twitter_access_token.php");

// 결과 확인
switch($connection -> http_code) {

    case 200 :

        // 성공, token 저장
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        // 인증 url 확인
        $url = $connection->getAuthorizeURL($token);

        // 인증 url (로그인 url) 로 redirect
        header("Location: " . $url);

    break;

    default:

        echo "Could not connect to Twitter. Refresh the page or try again later.";

    break;
}
?>