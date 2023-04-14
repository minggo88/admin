<?php
require __DIR__.'/../../lib/TradeApi.php';

$social_type = setDefault(loadParam('social_type'), '');

switch($social_type)  {
    case 'kakao':
        $login_url ='/oauth/authorize?client_id='.SOCIAL_LOGIN_KAKAO_API_KEY.'&redirect_uri='.urlencode(SOCIAL_LOGIN_KAKAO_REDIRECT_URI).'&response_type=code';
        $redirect_url =SOCIAL_LOGIN_KAKAO_REDIRECT_URI;
        break;
}

$tradeapi->success(array('login_url'=>$login_url,'redirect_url'=>$redirect_url));