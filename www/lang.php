<?php
    setcookie('lang', $_GET['lang'], null, '/');    
    if (isset($_SERVER['HTTP_REFERER'])) {    
        $prevPage = $_SERVER["HTTP_REFERER"];
        header("location:".$prevPage);
    }else{
        header("location: /");
    }
?>