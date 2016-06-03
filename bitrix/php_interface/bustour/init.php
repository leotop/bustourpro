<?php

//для облака. получаем название компании из урла
function getCompanyNameFromURL() {
    $res = false;
    global $APPLICATION;
    $page = $APPLICATION->GetCurPage();
    $name = explode(".bustourpro.ru",$_SERVER["HTTP_HOST"]);
    if ($name[0] != "") {
       $res = $name[0]; 
    }
    return $res; 
}

//получаем ID компании по урлу
function getCompanyIdByName($name) {
    $res = false;  
    $company = CIBlockELement::GetList(array(),array("NAME"=>$name),false,false,array("ID"))->Fetch();      
    if ($company["ID"] > 0) {
       $res = $company["ID"]; 
    }                     
    return $res;
}     

 

require_once dirname(__FILE__) .'/functions.php';
require_once dirname(__FILE__) .'/request.php';

