<?php

  $letters = '123456789'; // алфавит

  $caplen = 6; //длина текста
  $width = 140; $height = 50; //ширина и высота картинки
  $font = 'SketchFlow.ttf';//шрифт текста
  $fontsize = 24;// размер текста

  header('Content-type: image/png'); //тип возвращаемого содержимого (картинка в формате PNG) 
  $im = imagecreatefrompng('bg_capcha.png'); //создаёт новое изображение
  #$im = imagecreatetruecolor($width, $height); //создаёт новое изображение
  imagesavealpha($im, true); //устанавливает прозрачность изображения
  $bg = imagecolorallocatealpha($im, 0, 0, 0, 127); //идентификатор цвета для изображения
  imagefill($im, 0, 0, $bg); //выполняет заливку цветом
  
  putenv( 'GDFONTPATH=' . realpath('.') ); //проверяет путь до файла со шрифтами

  $captcha = '';//обнуляем текст
  for ($i = 0; $i < $caplen; $i++)
  {
    $captcha .= $letters[ rand(0, strlen($letters)-1) ]; // дописываем случайный символ из алфавила 
    $x = ($width - 20) / $caplen * $i + 5;//растояние между символами
    $x = rand($x, $x+2);//случайное смещение
    $y_temp = $height - ( ($height - $fontsize) / 2 ); // координата Y
	$y = rand($y_temp-4, $y_temp+4);
    $curcolor = imagecolorallocate( $im, rand(0, 200), rand(0, 200), rand(0, 200) );//цвет для текущей буквы
    $angle = rand(-15, 15);//случайный угол наклона 
    imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]); //вывод текста
  }

  // открываем сессию для сохранения сгенерированного текста
  session_start();
  $_SESSION['capcha'] = $captcha;

  imagepng($im); //выводим изображение
  imagedestroy($im);//отчищаем память


?>
