<?php
session_start();

header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");
header("Last-Modified: Mon, 23 Jul 1993 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: image/png");

$security_code = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

unset($_SESSION['captcha']);
$_SESSION['captcha'] = md5($security_code);

$red = 0;
$green = 0;
$blue = 0;

$captcha_image = @imagecreatefrompng('captcha_image_bg.png');

$canvas = imagecreatetruecolor(96, 24);
imagecopyresampled($canvas, $captcha_image, 0, 0, 0, 0, 96, 24, 60, 16);

$col = imagecolorallocate($captcha_image, $red, $green, $blue);
imagestring($captcha_image, 14, 5, 1, $security_code, $col);

$dst = imagecreatetruecolor( 96, 24);
imagecopyresampled($dst, $captcha_image, 0, 0, 0, 0, 96, 24, 60, 16);

$offset = rand(0,30);
$graph = true;
for($i=1; $i<=96; $i++){

	if($offset>0 && !$graph){
		$offset--;
	}
	else{
		$graph = true;
	}

	if($offset<30 && $graph){
		$offset++;
	}
	else{
		$graph = false;
	}

	$sin = sin($offset*6)*2;

	imagecopy($canvas, $dst, $i, 4 + $sin, $i, 4, 3, 16);
}

$col2 = imagecolorallocate($canvas, $red, $green, $blue);
imagerectangle($canvas, 0, 0, 95, 23, $col2);

imagepng($canvas);

imagedestroy($canvas);
?>