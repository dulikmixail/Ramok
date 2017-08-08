<?

#Работы портфолио

$id_portfolio = $_REQUEST['id_portfolio'];



if($id_portfolio){
	#Достаем работу
	$query = mysql_query("SELECT * FROM tbl_portfolio WHERE id='" .$id_portfolio. "'");
	$result = mysql_fetch_array($query);
	$name = $result['name'];
	$text = $result['text'];
	
	$page_title = $name;
	$title = $name;
	$page_navigate = '<a href="/portfolio">Портфолио</a> ‹‹ '.$name;
	
	$query = mysql_query("SELECT * FROM tbl_portfolio_img WHERE id_portfolio='" .$id_portfolio. "' ORDER BY rate");
	while($result = mysql_fetch_array($query)){
		$list_photo .= '<a class="asd" rel="gal" href="/images/portfolio/' .$result['src']. '"><img alt="' .$result['text_ru']. '" align="left" src="/images/portfolio/small/' .$result['src']. '" /></a>
		';
		}
	$list_photo .= '<br><br><br><br>';
	}
else{
	$page_title = 'Портфолио';
	$title = 'Портфолио';
	$page_navigate = 'Портфолио';
	
	
	#Достаем работы портфолио
	$query = mysql_query("SELECT *, (SELECT src FROM tbl_portfolio_img WHERE tbl_portfolio_img.id_portfolio=tbl_portfolio.id ORDER BY rate DESC LIMIT 1) AS img FROM tbl_portfolio ORDER BY date DESC");
	$i=1;
	$list_work = '<table><tr>';
	while($result = mysql_fetch_array($query)){
		$list_work .= '
		<td width="50%" valign="top">
		<div class="work">
			<a href="/portfolio/' .$result['id']. '-' .$result['caption']. '"><img alt="' .$result['name']. '" align="left" src="/images/portfolio/small/' .$result['img']. '"/><strong>' .$result['name']. '</strong></a>
			<p>' .$result['teaser']. '</p>
		</div>
		</td>
		';
		$i++;
		if($i%2){
			$list_work .= '</tr><tr>';
			}
		}
	$list_work .= '</table>';	
	}




?>