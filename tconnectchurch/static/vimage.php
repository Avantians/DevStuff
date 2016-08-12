<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by.0/ Creative Commons
 *
 * [v02-02/07/2011]:: he Web Help .com
 *  ------------------------------------------------------------------------- */
header('Content-type: image/jpeg');

$width = 50;
$height = 25;

$my_image = imagecreatetruecolor($width, $height);

imagefill($my_image, 0, 0, 0xFFFFFF);

// add noise
for ($c = 0; $c < 40; $c++){
	$x = rand(0,$width-1);
	$y = rand(0,$height-1);
	imagesetpixel($my_image, $x, $y, 0x000000);
	}

$x = rand(1,12);
$y = rand(1,12);

$rand_string = rand(1000,9999);
imagestring($my_image, 5, $x, $y, $rand_string, 0x000000);

setcookie('tntcon',(md5($rand_string).'a4xn'));

imagejpeg($my_image);
imagedestroy($my_image);
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!