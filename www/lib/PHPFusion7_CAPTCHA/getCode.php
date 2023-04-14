<?php

function getCode()
{
	require_once("config.php");
	require_once("securimage/multisite_include.php");

	$_db_link = null;

	$_db_link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

	$text_color = "#ff0000";
	$text_x_start = 8;
	$image_width = 140;
	$image_height = 45;

	$im = imagecreate($image_width, $image_height);
	$font_size = 24;
	$multi_text_color = "#0a68dd,#f65c47,#8d32fd";
	$text_angle_minimum = -20;
    $text_angle_maximum = 20;
    $use_multi_text = true;
    $ttf_file = "../securimage/elephant.ttf";
    $text_minimum_distance = 30;
    $text_maximum_distance = 33;

	$result = mysqli_query($_db_link,"SELECT captcha_string FROM ".DB_CAPTCHA." WHERE captcha_ip='" .$_COOKIE['token'] ."' order by captcha_datestamp desc limit 1") or die(mysqli_error($_db_link));
	if (mysqli_num_rows($result))
	{
		$data = mysqli_fetch_assoc($result);
		$code = strtoupper($data['captcha_string']);

		$font_color = imagecolorallocate($im, hexdec(substr($text_color, 1, 2)), hexdec(substr($text_color, 3, 2)), hexdec(substr($text_color, 5, 2)));

              $x = $text_x_start;
              $strlen = strlen($code);
              $y_min = ($image_height / 2) + ($font_size / 2) - 2;
              $y_max = ($image_height / 2) + ($font_size / 2) + 2;
              $colors = explode(',', $multi_text_color);

              for($i = 0; $i < $strlen; ++$i)
              {
                $angle = rand($text_angle_minimum, $text_angle_maximum);
                $y = rand($y_min, $y_max);
                if ($use_multi_text == true)
                {
                  $idx = rand(0, sizeof($colors) - 1);
                  $r = substr($colors[$idx], 1, 2);
                  $g = substr($colors[$idx], 3, 2);
                  $b = substr($colors[$idx], 5, 2);
                  $font_color = imagecolorallocate($im, hexdec($r), hexdec($g), hexdec($b));
                }

                @imagettftext($im, $font_size, $angle, $x, $y, $font_color, $ttf_file, $code{$i});

                $x += rand($text_minimum_distance, $text_maximum_distance);
            }

		return $code;
	}
	else
	{
		return "";
	}
}

$captchaCode = getCode();

echo $captchaCode;