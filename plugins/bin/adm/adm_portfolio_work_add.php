<?


#Добавление работы портфолио


include("../plugins/img_resize.php");
include('../plugins/_translit.php');

$id_category = $_REQUEST['id_category'];
$update = $_REQUEST['update'];
$id = $_REQUEST['id'];
$delete = $_REQUEST['delete'];
$deletemain = $_REQUEST['deletemain'];
$backurl = $_SESSION['backurl_workadd'];
$a = $_REQUEST['a'];


if($delete){
    $filePath = "../images/portfolio/" .$delete;
	@unlink($filePath);
    mysql_query("update tbl_portfolio set img='' where id='" .$id. "'");
	$notify = 'Файл был удален';
	}
if($deletemain){
    $filePath = "../images/portfoliomain/" .$deletemain;
	@unlink($filePath);
    mysql_query("update tbl_portfolio set imgmain='' where id='" .$id. "'");
	$notify = 'Файл был удален';
	}

#Добавление картинок
if($a=='addimg'){
	$i=1;
	while($i<=8){
		$filename = $_FILES['img'.$i]['tmp_name'];
		$imgname = $_FILES['img'.$i]['name'];
		if($filename){
			$new_name = md5(rand(1111111,9999999)).'.jpg';
			$new_big = '../images/portfolio/'.$new_name;
			$new_small = '../images/portfolio/small/'.$new_name;
			#copy(filename, $new_big);
			#copy(filename, $new_small);
			resizeImage($filename, $new_big, 600, 8000, 1, 0, 0);
			resizeImage($filename, $new_small, 150, 150, 1, 0, 0);
			$query = mysql_query("INSERT INTO tbl_portfolio_img SET id_portfolio='" .$id. "', src='" .$new_name. "'");
			}
		$i++;
		}
	header("Location: /adm/portfolio/work/add?id=".$id);
	exit();
	}

#Редактирование картинок
if($a=='editimg'){
	$query = mysql_query("SELECT id FROM tbl_portfolio_img WHERE id_portfolio='" .$id. "'");
	while($result=mysql_fetch_array($query)){
		$rate = $_POST['rate'.$result['id']];
		$text_ru = $_POST['text'.$result['id'].'_ru'];
		$text_eng = $_POST['text'.$result['id'].'_eng'];
		$text_pl = $_POST['text'.$result['id'].'_pl'];		
		$query2 = mysql_query("UPDATE tbl_portfolio_img SET rate='" .$rate. "', text_ru='" .$text_ru. "' WHERE id='" .$result['id']. "'");
		
		$filename = $_FILES['img'.$result['id']]['tmp_name'];
		$imgname = $_FILES['img'.$result['id']]['name'];
		if($filename){
			$file_type = substr($imgname, -3, 3);
			$new_name = md5(rand(1,9999999)).'.'.$file_type;
			$new_big = '../images/portfolio/'.$new_name;
			$new_small = '../images/portfolio/small/'.$new_name;
			resizeImage($filename, $new_big, 600, 8000, 1, 0, 0);
			resizeImage($filename, $new_small, 150, 150, 1, 0, 0);
			#Удаляем старые картинки
			$query2 = mysql_query("SELECT src FROM tbl_portfolio_img WHERE id='" .$result['id']. "'");
			$src_old = mysql_result($query2, '0', 'src');
			@unlink('../images/portfolio/'.$src_old);
			@unlink('../images/portfolio/small/'.$src_old);
			#Обновляем запись с картинкой
			$query2 = mysql_query("UPDATE tbl_portfolio_img SET src='" .$new_name. "' WHERE id='" .$result['id']. "'");
			}		
		
		}
	header("Location: /adm/portfolio/work/add?id=".$id);
	exit();	
	}
	
#Делаем картинку главной в портфолио
if($a=='mainimg'){
	$idimg = $_REQUEST['idimg'];
	$query = mysql_query("SELECT src FROM tbl_portfolio_img WHERE id='" .$idimg. "'");
	$src = mysql_result($query, '0', 'src');
	$query = mysql_query("UPDATE tbl_portfolio SET main_image='" .$src. "' WHERE id='" .$id. "'");
	$query = mysql_query("UPDATE tbl_portfolio_img SET is_main='0' WHERE id_portfolio='" .$id. "'");
	$query = mysql_query("UPDATE tbl_portfolio_img SET is_main='1' WHERE id='" .$idimg. "'");
	header("Location: /adm/portfolio/work/add?id=".$id);
	exit();	
	}

#Удаляем картинку
if($a=='delimg'){
	$idimg = $_REQUEST['idimg'];
	$query = mysql_query("SELECT src FROM tbl_portfolio_img WHERE id='" .$idimg. "'");
	$src = mysql_result($query, '0', 'src');
	$query = mysql_query("DELETE FROM tbl_portfolio_img WHERE id='" .$idimg. "'");
	@unlink('../images/portfolio/'.$src);
	@unlink('../images/portfolio/small/'.$src);	
	header("Location: /adm/portfolio/work/add?id=".$id);
	exit();	
	}


if($update){
    $head_text = 'Редактирование работы';
	$caption = encodestring($_POST['name']);
	$name = mysql_real_escape_string($_POST['name']);
	
	
	$teaser = mysql_real_escape_string($_POST['teaser']);
	$text = mysql_real_escape_string($_POST['text']);
	$date = $_POST['date'];
	$show_main = $_POST['show_main'];
	$url = $_POST['url'];

	$PageDescription = mysql_real_escape_string($_REQUEST['PageDescription']);
	$PageTitleSEO = mysql_real_escape_string($_REQUEST['PageTitleSEO']);
	$PageKeywords = mysql_real_escape_string($_REQUEST['PageKeywords']);
	$PageAuthor = mysql_real_escape_string($_REQUEST['PageAuthor']);
	$PageRobots = mysql_real_escape_string($_REQUEST['PageRobots']);		
	
	$img_old = $_REQUEST['img_old'];
	$imgmain_old = $_REQUEST['imgmain_old'];

	$img = $_FILES['img']['tmp_name'];
	$img_name = $_FILES['img']['name'];
	$imgmain = $_FILES['imgmain']['tmp_name'];
	$imgmain_name = $_FILES['imgmain']['name'];
	
	$date = date("Y-m-d", strtotime($date));
	
	if($img_name){
		$umd="".md5(uniqid(rand())).".jpg";
		$img_name = $umd;
		$filePath = "../images/portfolio/" .$img_name;
		move_uploaded_file($img, $filePath);
		resizeImage($filePath, $filePath, 698, 218, 1, 0, 0);
		
		if(strlen($img_old)>0){
			$filePath = "../images/portfolio/" .$img_old;
			if(file_exists($filePath))unlink($filePath); 
			}
		$query = mysql_query("UPDATE tbl_portfolio SET img='" .$img_name. "' WHERE id='" .$id. "'");
		}
	if($imgmain_name){
		$umd="".md5(uniqid(rand())).".png";
		$imgmain_name = $umd;
		$filePath = "../images/portfoliomain/" .$imgmain_name;
		move_uploaded_file($imgmain, $filePath);
		#resizeImage($filePath, $filePath, 100, 100, 1, 0, 0);
		
		if(strlen($imgmain_old)>0){
			$filePath = "../images/portfoliomain/" .$imgmain_old;
			if(file_exists($filePath))unlink($filePath); 
			}
		$query = mysql_query("UPDATE tbl_portfolio SET imgmain='" .$imgmain_name. "' WHERE id='" .$id. "'");
		}
	
	if($id){
		$query = mysql_query("UPDATE tbl_portfolio SET id_category='" .$id_category. "', name='" .$name. "', caption='" .$caption. "', url='" .$url. "', teaser='" .$teaser. "', text='" .$text. "', date='" .$date. "', show_main='" .$show_main. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "' WHERE id='" .$id. "'");
		header('Location: /adm/portfolio/work/add?a=ready&id='.$id);
		exit();
		}
	else{
		$query = mysql_query("INSERT INTO tbl_portfolio SET id_category='" .$id_category. "', name='" .$name. "', caption='" .$caption. "', url='" .$url. "', teaser='" .$teaser. "', text='" .$text. "', date='" .$date. "', show_main='" .$show_main. "', img='" .$img_name. "', imgmain='" .$imgmain_name. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "'");
		$id = mysql_insert_id();
		header('Location: /adm/portfolio/work/add?a=ready&id='.$id);
		exit();	
		}
	}
else{
	if($id){
        $head_text = 'Редактирование работы';
		#Достаем информацию о товаре
		$query = mysql_query("SELECT * FROM tbl_portfolio WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		$name = $result['name'];
		$teaser = $result['teaser'];
		$text = $result['text'];
		$date = $result['date'];
		$img = $result['img'];
		$imgmain = $result['imgmain'];

		$PageDescription = $result['PageDescription'];
		$PageTitleSEO = $result['PageTitleSEO'];
		$PageKeywords = $result['PageKeywords'];
		$PageAuthor = $result['PageAuthor'];
		$PageRobots = $result['PageRobots'];		

		$id_category = $result['id_category'];
		$show_main = $result['show_main'];
		$url = $result['url'];
		
		if($show_main){
			$chk_show_main = ' checked ';
			}
		
		$date = date("d.m.Y", strtotime($date));
		
		if($img){
			$show_img = '
			<tr>
				<td nowrap class=TableRow1 >Загруженное изображение: </td>
				<td class=TableRow1 align=left>
					' .$img. '<br />
					<img width="100%" src="/images/portfolio/' .$img. '"><br />
					<a href="/adm/portfolio/work/add?id=' .$id. '&delete=' .$img. '" style="float:right;">Удалить</a>
				</td>
			</tr>';
			}
		if($imgmain){
			$show_imgmain = '
			<tr>
				<td nowrap class=TableRow1 >Загруженное изображение: </td>
				<td class=TableRow1 align=left>
					' .$imgmain. '<br />
					<img src="/images/portfoliomain/' .$imgmain. '"><br />
					<a href="/adm/portfolio/work/add?id=' .$id. '&deletemain=' .$imgmain. '" style="float:right;">Удалить</a>
				</td>
			</tr>';
			}

	#Достаем изображения
	$query = mysql_query("SELECT * FROM tbl_portfolio_img WHERE id_portfolio='" .$id. "' ORDER BY rate");
	while($result=mysql_fetch_array($query)){
		if($result['is_main']){$imgbold=' style="border:5px solid #999"';}else{$imgbold='';}
		$list_image .= '
		<tr>
			<td><a target="_blank()" href="/images/portfolio/' .$result['src']. '"><img border="0" ' .$imgbold. ' src="/images/portfolio/small/' .$result['src']. '"></a></td>
			<td>
				<input class=txt name="text' .$result['id']. '_ru" type="text" value="' .$result['text_ru']. '" style="width:300px"> 
				<input class=txt name="rate' .$result['id']. '" type="text" value="' .$result['rate']. '"  style="width:100px"> <a style="color:red" href="/adm/portfolio/work/add?a=delimg&id=' .$id .'&idimg=' .$result['id']. '">Удалить</a>
			</td>
		</tr>';
		}
		
		}
	else{
        $head_text = 'Добавление работы';		
		}
	}	



?>
