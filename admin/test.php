<?

$db_connection = mysql_connect('localhost', 'root', '');
mysql_select_db('ramok2', $db_connection);

# �������� ������ ������ 
$query = mysql_query("SELECT * FROM rubr_file");
while($result = mysql_fetch_array($query)){
	if($result['id'] != $_GET['r']){
		$rubr .= ' <a href="?r=' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px">' .$result['rubr_name']. ';</span></a> ';
		}
	else{
		$rubr .= ' <a style="color:red" href="?r=' .$result['id']. '"><span style="font-family:Geneva, Arial, Helvetica, sans-serif; font-size:13px">' .$result['rubr_name']. ';</span></a> ';
		}
	}

$content .= '
<img src="/cfg/image/true2.gif">
<span style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><em>
� ���� ������� �� ��������� ����� ��� ���������� (�������� ��� ������ ������������, ����������, �����������). ��� ����� ������� �� ��������� �������: </em></span>
<br><br>
' .$rubr. '<br><br>
<table cellpadding="0" cellspacing="0" class="content" width="100%">
	<tr>
		<td class="topic"><strong>������� ��������</strong></td>
		<td class="topic"><strong>������</strong></td>
	</tr>';

if(!empty($_GET['r'])){
	$query = mysql_query("SELECT * FROM tbl_files WHERE rubr=" .$_GET['r']. " ORDER BY id");
	}
else{
	$query = mysql_query("SELECT * FROM tbl_files ORDER BY id");
	}

while($result = mysql_fetch_array($query)){
	$date = strtotime($result['date']);
	$size = round($result['size'] / 1000);
	$date = date("d.m.Y", $date);
	
	$content  .= '
	<tr>
		<td height="25" valign="center" class="topic" ><a target="_blank" href="/files/' .$result['name']. '" class="link_menu">
		<img border="0" src="/cfg/image/price.gif"> ' .$result['caption']. '<br><span style="color:#666666"> (' .$result['name']. ') </span>
		</td>
		<td width="60" class="topic" valign="center" align="center" valign="top" width="100"><a class="link" href="/files/' .$result['name']. '" class="link_menu"> [' .$size. ' Kb]</a></td>
	</tr>
	';
	}
	
$content .= '</table>';


?>