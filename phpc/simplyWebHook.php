<?php

//$queryUrl = "https://ramok.bitrix24.by/rest/110/gp8dqyml6gmukfzb/task.item.add.json?".http_build_query(
//        array(
//            "arNewTaskData" => array(
//                "TITLE" => "Тестовая задача",
//                "DESCRIPTION" => "Описание задачи",
//                "RESPONSIBLE_LAST_NAME" =>"Дулик",
//            ))
//);
$queryUrl = "https://ramok.bitrix24.by/rest/110/gp8dqyml6gmukfzb/user.get.json?".http_build_query(
    array("LAST_NAME" =>"Дулик")
    );

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER =>0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER =>0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl,
));


$result = curl_exec($curl);
curl_close($curl);

echo "<pre>";
print_r($result);
echo "<br>";
print_r(iconv("UTF-8", "WINDOWS-1251",implode(json_decode($result,true))));
echo "<pre>";



