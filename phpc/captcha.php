<?php

  $letters = '123456789'; // �������

  $caplen = 6; //����� ������
  $width = 140; $height = 50; //������ � ������ ��������
  $font = 'SketchFlow.ttf';//����� ������
  $fontsize = 24;// ������ ������

  header('Content-type: image/png'); //��� ������������� ����������� (�������� � ������� PNG) 
  $im = imagecreatefrompng('bg_capcha.png'); //������ ����� �����������
  #$im = imagecreatetruecolor($width, $height); //������ ����� �����������
  imagesavealpha($im, true); //������������� ������������ �����������
  $bg = imagecolorallocatealpha($im, 0, 0, 0, 127); //������������� ����� ��� �����������
  imagefill($im, 0, 0, $bg); //��������� ������� ������
  
  putenv( 'GDFONTPATH=' . realpath('.') ); //��������� ���� �� ����� �� ��������

  $captcha = '';//�������� �����
  for ($i = 0; $i < $caplen; $i++)
  {
    $captcha .= $letters[ rand(0, strlen($letters)-1) ]; // ���������� ��������� ������ �� �������� 
    $x = ($width - 20) / $caplen * $i + 5;//��������� ����� ���������
    $x = rand($x, $x+2);//��������� ��������
    $y_temp = $height - ( ($height - $fontsize) / 2 ); // ���������� Y
	$y = rand($y_temp-4, $y_temp+4);
    $curcolor = imagecolorallocate( $im, rand(0, 200), rand(0, 200), rand(0, 200) );//���� ��� ������� �����
    $angle = rand(-15, 15);//��������� ���� ������� 
    imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]); //����� ������
  }

  // ��������� ������ ��� ���������� ���������������� ������
  session_start();
  $_SESSION['capcha'] = $captcha;

  imagepng($im); //������� �����������
  imagedestroy($im);//�������� ������


?>
