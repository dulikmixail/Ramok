<?


#Категории портфолио - добавление

include('../plugins/_translit.php');
$update = $_REQUEST['update'];
$id = $_REQUEST['id'];
$delete = $_REQUEST['delete'];

if($update){
    $head_text = 'Редактирование категории';
	$name = $_REQUEST['name'];
	$teaser = $_REQUEST['teaser'];
	$caption = encodestring($name);
		
	$PageDescription = $_REQUEST['PageDescription'];
	$PageTitleSEO = $_REQUEST['PageTitleSEO'];
	$PageKeywords = $_REQUEST['PageKeywords'];
	$PageAuthor = $_REQUEST['PageAuthor'];
	$PageRobots = $_REQUEST['PageRobots'];		
		
	if($id){
		$query = mysql_query("UPDATE tbl_portfoliorubr SET name='" .$name. "', teaser='" .$teaser. "', caption='" .$caption. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "'  WHERE id='" .$id. "'");
		header("Location: /adm/portfolio/category");
		exit();
		}
	else{
		$query = mysql_query("INSERT INTO tbl_portfoliorubr SET name='" .$name. "', teaser='" .$teaser. "', caption='" .$caption. "', PageDescription='" .$PageDescription. "', PageTitleSEO='" .$PageTitleSEO. "', PageKeywords='" .$PageKeywords. "', PageAuthor='" .$PageAuthor. "', PageRobots='" .$PageRobots. "'");
		header("Location: /adm/portfolio/category");
		exit();		
		}
	}
else{
	if($id){
        $head_text = 'Редактирование категории';
		#Достаем информацию о товаре
		$query = mysql_query("SELECT * FROM tbl_portfoliorubr WHERE id='" .$id. "'");
		$result = mysql_fetch_array($query);
		$name = $result['name'];
		$teaser = $result['teaser'];
		$PageDescription = $result['PageDescription'];
		$PageTitleSEO = $result['PageTitleSEO'];
		$PageKeywords = $result['PageKeywords'];
		$PageAuthor = $result['PageAuthor'];
		$PageRobots = $result['PageRobots'];		

		
		}
	else{
        $head_text = 'Добавление категории';		
		}
	
	}




?>
