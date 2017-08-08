<?
$convert_path=ADM_IM_PATH;

function resizeImage($src_file, $dest_file, $maxwidth, $maxheight, $is_resampled=0, $is_crop=0, $use_imagemagick=0){
   	global $convert_path;
   	global $resizeImageErr;
	$resizeImageErr='no errors';

	$maxwidth_orig = $maxwidth;

	if (!is_readable($src_file)){
		$resizeImageErr="file isn't readable or not found: ".$src_file;
		return 0;
	}else{
		if($dest_file){
			if (!is_writable(dirname($dest_file))){
				$resizeImageErr="folder isn't writable: ".dirname($dest_file);
				return 0;
			}
		}
		$size=GetImageSize($src_file);
		if(!$size){
			$resizeImageErr='invalid image format';
			return 0;
		}
		$src_width = $size[0];
		$src_height = $size[1];
		$type = $size[2];
		if($type>3){
			$resizeImageErr='image extension is not supported';
			return 0;
		}
		$crop_w=0;
		$crop_h=0;
		$hc_w=0;
		$hc_h=0;
		
		if ($maxwidth >= $src_width && $maxheight >= $src_height){
			#if($src_file != $dest_file)copy($src_file, $dest_file);
			if($src_width>250 && $src_height>250){
				if($type==1){
					$src = imagecreatefromgif($src_file);
					imagejpeg($src,$src_file,100);
					imagedestroy($src);			
					$src = imagecreatefromjpeg($src_file);
					}
				if($type==2){
					$src=imagecreatefromjpeg($src_file);
					}
				if($type==3){
					$src=imagecreatefrompng($src_file);
					imagejpeg($src,$src_file,100);
					imagedestroy($src);		
					$src = imagecreatefromjpeg($src_file);
					}
				#$im = imagecreate($src_width,$src_height);
				#imagecopyresampled($im,$src,0,0,0,0,$src_width,$src_height,$src_width,$src_height);
				if($maxwidth_orig>=300){
					$logoImage = ImageCreateFromPNG('../images/h2o.png');
					imagecopy($src, $logoImage, $src_width-149, $src_height-69, 0, 0, 159, 69);
					}
				imagejpeg($src,$src_file,100);
				imagedestroy($src);
				return 1;
				}
		}else{
		#if(1==1){
			if($maxwidth > $maxheight) $ratios['dest'] = array('pos' => 'hor', 'rate' => $maxwidth / $maxheight);
			elseif ($maxwidth == $maxheight) $ratios['dest'] = array('pos' => 'sqa', 'rate' => 1);
			else $ratios['dest'] = array('pos' => 'ver', 'rate' => ($maxheight / $maxwidth));
	
			if($src_width > $src_height) $ratios['src'] = array('pos' => 'hor', 'rate' => ($src_width / $src_height));
			elseif($src_width == $src_height) $ratios['src'] = array('pos' => 'sqa', 'rate' => 1);
			else $ratios['src'] = array('pos' => 'ver', 'rate' => ($src_height / $src_width));
	
			$maxw = $maxwidth;
			$maxh = $maxheight;
			$src_h = $src_height;
			$src_w = $src_width;
			$params = array('w','h');
			
			if(($ratios['dest']['pos'] == 'hor' && $ratios['src']['pos'] == 'hor' && ($ratios['dest']['rate'] > $ratios['src']['rate'])) 
			|| ($ratios['dest']['pos'] == 'ver' && $ratios['src']['pos'] == 'ver' && ($ratios['dest']['rate'] < $ratios['src']['rate'])) 
//			|| ($ratios['dest']['pos'] == 'ver' && $ratios['src']['pos'] == 'hor'/* && ($ratios['dest']['rate'] > $ratios['src']['rate'])*/)
			|| ($ratios['dest']['pos'] == 'hor' && $ratios['src']['pos'] == 'ver'/* && ($ratios['dest']['rate'] > $ratios['src']['rate'])*/)
			|| ($ratios['dest']['pos'] == 'hor' && $ratios['src']['pos'] == 'sqa')
			|| ($ratios['dest']['pos'] == 'sqa' && $ratios['src']['pos'] == 'ver')
			) $params = array('h','w');
			
			if($is_crop)$params = array_reverse($params);
		
			${'dest_'.$params[0]} = ${'max'.$params[0]};
			$ratio = ${'src_'.$params[0]} / ${'max'.$params[0]};
			${'dest_'.$params[1]}=${'src_'.$params[1]}/$ratio;

			if($is_crop)
			{
				if (${'dest_'.$params[1]} <= ${'max'.$params[1]}) ${'crop_'.$params[1]} = 0;
				else ${'crop_'.$params[1]} = ${'dest_'.$params[1]} - ${'max'.$params[1]};
			}
			
			$w=floor($dest_w-$crop_w);
			$h=floor($dest_h-$crop_h);
			
			if ($is_crop==2){
				$hc_w=floor($crop_w/2);
				$hc_h=floor($crop_h/2);
			}
			$crop_w=floor($crop_w);
			$crop_h=floor($crop_h);
	
			$dest_width=floor($dest_w);
			$dest_height=floor($dest_h);
	
			if($use_imagemagick){
				if(!$convert_path){
					$resizeImageErr='ImageMagick utility not found';
					$use_imagemagick=0;
					$add_err=" and ImageMagick is not found";
				}else{
					$dest_width += 2;
					$dest_height += 2;
					if(!$dest_file){
						$tmpfname = tempnam(ini_get('upload_tmp_dir'),"resize_pic");
						exec("$convert_path $src_file -resize {$dest_width}x{$dest_height} -crop {$w}x{$h}+$hc_w+$hc_h +repage $tmpfname");
						readfile($tmpfname);
						unlink($tmpfname);
						return 1;
					}else{
						exec("$convert_path $src_file -resize {$dest_width}x{$dest_height} -crop {$w}x{$h}+$hc_w+$hc_h +repage $dest_file");
						return 1;
					}
				}
			}
			if(!$use_imagemagick){
				ob_start();
				phpinfo(8);
				$phpinfo=ob_get_contents();
				ob_end_clean();
				$phpinfo=strip_tags($phpinfo);
				$phpinfo=stristr($phpinfo,"gd version");
				$phpinfo=stristr($phpinfo,"version");
				$end=strpos($phpinfo,".");
				$phpinfo=substr($phpinfo,0,$end);
				$length = strlen($phpinfo)-1;
				$phpinfo=substr($phpinfo,$length);
				if($phpinfo<2){
					$resizeImageErr="GD 2 or higher is required".$add_err;
					return 0;
				}else{
					if($type==1){
						if (!function_exists('imagegif')){
							if($convert_path){
								exec($convert_path." $src_file -resize {$dest_width}x{$dest_height} -crop {$w}x{$h}+$hc_w+$hc_h +repage $dest_file");
								return 1;
							}else{
								$resizeImageErr="GD Lib doesn't support creating GIF files and ImageMagick utility not found";
								return 0;
							}
						}else{
							$src = imagecreatefromgif($src_file);
							if (imageIsTrueColor($src)) $im = imagecreatetruecolor($dest_width-$crop_w,$dest_height-$crop_h);
							else $im = imagecreate($dest_width-$crop_w,$dest_height-$crop_h);
							if ($is_resampled) imagecopyresampled($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height);
							else imagecopyresized($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height);
							if($maxwidth_orig>=300){
								$logoImage = ImageCreateFromPNG('../images/h2o.png');
								imagecopy($im, $logoImage, $dest_width-149, $dest_height-69, 0, 0, 159, 69);
								}
							if($dest_file) imagejpeg($im,$dest_file);
							else imagegif($im);
							imagedestroy($im);
							return 1;
						}
					}elseif($type==2) {
						if ($size["channels"]==4){
							$resizeImageErr="please convert the image: $src_file from CMYK into RGB";
							return 0;
						}
						$src=imagecreatefromjpeg($src_file);
						if(imageIsTrueColor($src)) $im=imagecreatetruecolor($dest_width-$crop_w,$dest_height-$crop_h);
						else $im=imagecreate($dest_width-$crop_w,$dest_height-$crop_h);					
						if ($is_resampled) imagecopyresampled($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height);
						else imagecopyresized($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height);
						if($maxwidth_orig>=300){
							$logoImage = ImageCreateFromPNG('../images/h2o.png');
							imagecopy($im, $logoImage, $dest_width-149, $dest_height-69, 0, 0, 159, 69);
							}
						if($dest_file) imagejpeg($im,$dest_file,100);
						else imagejpeg($im);
						imagedestroy($im);
						return 1; 
					}elseif($type==3) {
						if (!function_exists("imageIsTrueColor")){
							$resizeImageErr='GD 2 or higher is required file: '.$src_file;
							return 0;
						}else{
							$src = imagecreatefrompng($src_file);
							if (imageIsTrueColor($src)) $im = imagecreatetruecolor($dest_width-$crop_w,$dest_height-$crop_h);
							else $im = imagecreate($dest_width-$crop_w,$dest_height-$crop_h);
							imageAlphaBlending($im,false);
							imageSaveAlpha($im,true);
							if ($is_resampled) imagecopyresampled($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height);
							else imagecopyresized($im,$src,-1*$hc_w,-1*$hc_h,0,0,$dest_width,$dest_height,$src_width,$src_height); 
							if($maxwidth_orig>=300){
								$logoImage = ImageCreateFromPNG('../images/h2o.png');
								imagecopy($im, $logoImage, $dest_width-149, $dest_height-69, 0, 0, 159, 69);
								}
							if($dest_file) imagejpeg($im,$dest_file);
							else imagepng($im);
							imagedestroy($im);
							return 1;
						}  
					} 
				}
			}
		}
	}
}
?>