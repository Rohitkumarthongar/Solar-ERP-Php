<?php
$img = imagecreate(1024, 1024);
imagecolorallocate($img, 255, 255, 255);
imagepng($img, 'public/images/screenshot-mobile.png');
imagepng($img, 'public/images/screenshot-desktop.png');
