<?
set_time_limit(0);
$i = 1;
while ($i <= 5){
	
	# делаем привьюшку
	
	$filename =  'steklo/2/' .$i. '.jpg';
	list($width_orig, $height_orig) = getimagesize($filename);
	$width = 100;
	$height = 100;
	if ($width && ($width_orig < $height_orig)) {
    	$width = ($height / $height_orig) * $width_orig;
	} else {
    	$height = ($width / $width_orig) * $height_orig;
	}
	$image_p = imagecreatetruecolor($width, $height);
	$image = imagecreatefromjpeg($filename);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	$n = strlen($i);
	if ($n==1){$im='00' .$i;}
	elseif($n==2){$im='0' .$i;}
	else{$im = $i;}
	imagejpeg($image_p, "steklo/" .$im. "p.jpg", 100);
	
	# уменьшаем изображение
	
	$width = 500;
	$height = 500;
	
	if ($width && ($width_orig < $height_orig)) {
    	$width = ($height / $height_orig) * $width_orig;
	} else {
    	$height = ($width / $width_orig) * $height_orig;
	}
	
	$image_p = imagecreatetruecolor($width, $height);
	$image = imagecreatefromjpeg($filename);
	
	$black = imagecolorallocate($image, 0, 0, 0);
	
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	$logo_img = imagecreatefromgif("logo.gif");
	imagecopymerge($image_p, $logo_img, 0, 0, 0, 0, 215, 15, 40); 
	
	$n = strlen($i);
	if ($n==1){$im='00' .$i;}
	elseif($n==2){$im='0' .$i;}
	else{$im = $i;}
	imagejpeg($image_p, "steklo/" .$im. ".jpg", 100);
	print $im. ' converted<br>';

	$i++;
	}
exit();
?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
