<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
<title>Затемнение</title>

<style type="text/css">
#fon {
top:0;
left:0;
width:100%;
height:100%;
position:fixed;
background:#000;
display:none;
opacity: 0.7;
/*-moz-opacity: 0.8; для старых FF*/
filter: alpha(opacity = 70);
}
#mod {
top:50%;
left:50%;
margin-top:-100px;
margin-left:-100px;
width:200px;
height:200px;
position:fixed;
background:#fff;
display:none;
}
#mod input {
width:100px;
height:30px;
margin-top:85px;
margin-left:50px;

}
</style>
<script type="text/javascript">
function on() {
document.getElementById('fon').style.display='block';
document.getElementById('mod').style.display='block';
}
function off() {
document.getElementById('fon').style.display='none';
document.getElementById('mod').style.display='none';
}
</script>

</head>
<body>
<div id="inf">
<input type="button" value="Тест" onclick="on()" />
</div>
<div id="fon"></div>
<div id="mod"><input type="button" value="Нажми" onclick="off()" /></div>

</body>
</html>
