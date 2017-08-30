<?php

require "config.php";
require "function.php";
require "database.php";

$database=new Database;



if($_GET['s']=='submit') {

    $exception='';

    if(!preg_match('{\\d\\d\\d\\d\\d\\d\\d\\d\\d}',$_POST['unp'])){
        $exception .='УНП введен не верно <br>';
    }
    if(!preg_match('{(0?[1-9]|[12][0-9]|3[01])\.(0?[1-9]|1[012])\.((19|20)\\d\\d)}',$_POST['date'])){
        $exception .='Ведите дату в формате DD.MM.YYYY <br>';
    }
    if(!preg_match('{^(([0,1][0-9])|(2[0-3])):[0-5][0-9]$}',$_POST['time'])){
        $exception .='Введите время в формате НН:MM <br>';
    }

    if($exception==''){
        $date = DateTime::createFromFormat('d.m.Y', $_POST['date']);
        $query = mysql_query("SELECT * FROM tbl_req_SKNO WHERE date='".$date->format('Y-m-d')."' AND time='".$_POST['time']."'");
        if(mysql_num_rows($query)==0){
            $info='Ваша заявка принята';
            $query = mysql_query("INSERT INTO tbl_req_SKNO SET unp='".$_POST['unp']."',company='".$_POST['company']."',date='".$_POST['date']."',`time`='".$_POST['time']."';");
        } else {
            $exception='Данное время уже занято!';
        }

    }
}


$content .= '<head>
<meta charset="UTF-8">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="../js/datetimepicker/jquery.datetimepicker.js"></script>
<link type="text/css" href="../js/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" />
</head>';

$content .= '

<form action="?s=submit" method="POST">
    

    <label for="unp">УНП</label><input type="number" name="unp" id="unp" placeholder="123456789" value="123456789"><br>
    <label for="company">Комапния</label><input type="text" name="company" id="company" placeholder="ИП Рога и копыта" value="ИП Рога и копыта"><br>

    <label for="date">Выберите желаемую дату</label>

    <input id="date" name="date" type="text" value="" /><br>


    <label for="time" id="time_label" hidden>Время</label>
    <input id="time" name="time" type="text" value="" hidden/>
    <br>
    <label name="exception">'.$exception.'</label>
    <label name="info">'.$info.'</label>
    <input type="submit" value="Оправить">

</form>


<script type="text/javascript">
jQuery(\'#date\').focus(function() {
    jQuery(\'#time\').hide();
    jQuery(\'#time_label\').hide();
    jQuery(\'#time\').val("");
});


jQuery(\'#date\').change(function() {

if(jQuery(\'#date\').val().trim()!=""){

    sendDateJson();    

} else {
    jQuery(\'#time\').datetimepicker({
  timepicker:false,
});
}
    
});

jQuery(function(){
    
jQuery(\'#date\').datetimepicker({
  lang:\'ru\',
  timepicker:false,
  format:\'d.m.Y\',
  minDate: getDateToday(),
  closeOnDateSelect:true,
});

jQuery(\'#time\').datetimepicker({
  datepicker:false,
  format:\'H:i\',
  timepicker:false,
});

sendDateJson(); 

});



function onAjaxSuccess(data)
{
    
    var parsed = JSON.parse(data);
    var arr = [];
    for(var x in parsed){
        arr.push(parsed[x]);
    }
    
   if(arr.length!=0){
       jQuery(\'#time\').datetimepicker({
       datepicker:false,
       format:\'H:i\',
       timepicker:true,
       allowTimes: arr
   });
       jQuery(\'#time\').hide();
       jQuery(\'#time_label\').hide();
   }else {
    jQuery(\'#time\').datetimepicker({
    datepicker:false,
    format:\'H:i\',
    timepicker:false,
});
   }
  

     jQuery(\'#time_label\').show();
     jQuery(\'#time\').show();
}

function getDateValToJson() {
    var json = {date_value:jQuery(\'#date\').val()}
    return json
}

function getDateToday() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        
        var yyyy = today.getFullYear();
        if(dd<10){
            dd=\'0\'+dd;
        } 
        if(mm<10){
            mm=\'0\'+mm;
        } 
        today = dd+\'.\'+mm+\'.\'+yyyy;
        return today;
        
}

function sendDateJson() {
  $.ajax({
        type: "GET",
        url: "/phpc/ajaxtest.php",
        data: getDateValToJson(),
        success: onAjaxSuccess,
        async: true,
    });
}


</script>

';

$content .= '

';

echo $content;