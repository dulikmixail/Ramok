<?
$d1 = htmlspecialchars($_GET['d1']);
$d2 = htmlspecialchars($_GET['d2']);
$d3 = htmlspecialchars($_GET['d3']);

?>
<html>
<head>
<title>УП "Рамок". Изображение!</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta name="robots" content="index,follow">
<meta name="Author" content="УП 'Рамок'">
<meta name="description" content="Производственно-торговое частное унитарное предприятие 'Рамок'">
<meta http-equiv="Page-Enter" content="progid:DXImageTransform.Microsoft.GradientWipe(Duration=2)">
<meta http-equiv="Page-Exit" content="progid:DXImageTransform.Microsoft.GradientWipe(Duration=2)">
</head>
<body style="width:1000">
<table width="100%" height="100%" cellpadding="0" cellspacing="0" style="font-size:10">
	<tr> 
		<td align="center" valign="middle">
		<?
		print '<img src="/image/' .$d1. '/' .$d2. '.' .$d3. '">';
		$num_d2 = strlen($d2);
		if($num_d2 == '3'){
			$d2_1=$d2+1;
			$n=strlen($d2_1);
			if ($n==1){$d2_1='00' .$d2_1;}
			elseif($n==2){$d2_1='0' .$d2_1;}
			elseif($n==3){$d2_1=$d2_1;}
			
			$d2_2=$d2-1;
			$n=strlen($d2_2);
			if ($n==1){$d2_2='00' .$d2_2;}
			elseif($n==2){$d2_2='0' .$d2_2;}
			elseif($n=3){$d2_2=$d2_2;}
			}
		else{
			$d2_1 = $d2 + 1;
			$d2_2 = $d2 - 1;
			}
		?>
		<br><br>
		<?
		if (file_exists($d1. '/' .$d2_2. '.' .$d3. '')){
			print '<a href="/image/?d1=' .$d1. '&d2=' .$d2_2. '&d3=' .$d3. '"><strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px; text-decoration:none"><em>Предыдущая фотография</em></span></strong></a>';
			}
		?>
		 :::
		<?
		if (file_exists($d1. '/' .$d2_1. '.' .$d3. '')){
			print '<a href="/image/?d1=' .$d1. '&d2=' .$d2_1. '&d3=' .$d3. '"><strong><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px; text-decoration:none"><em>Следующая фотография</em></span></strong></a>';
			}
		?>
		</td>
	</tr>
</table>
</body>
</html>