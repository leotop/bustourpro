<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    if (CModule::IncludeModule("iblock"))
    {
        if($arParams["IBLOCK_ID"] > 0)
            $bWorkflowIncluded = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "WORKFLOW") == "Y" && CModule::IncludeModule("workflow");
        else
            $bWorkflowIncluded = CModule::IncludeModule("workflow");

        if (!$bWorkflowIncluded)
        {
            if ($arParams["STATUS_NEW"] != "N" && $arParams["STATUS_NEW"] != "NEW") $arParams["STATUS_NEW"] = "ANY";
        }

        if(!is_array($arParams["STATUS"]))
        {
            if($arParams["STATUS"] === "INACTIVE")
                $arParams["STATUS"] = array("INACTIVE");
            else
                $arParams["STATUS"] = array("ANY");
        }

        if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
        {
            $arrFilter = array();
        }
        else
        {
            $arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
            if(!is_array($arrFilter))
                $arrFilter = array();
        }



        $arGroups = $USER->GetUserGroupArray();

        if(!is_array($arParams["GROUPS"]))
            $arParams["GROUPS"] = array();

        $arGroups = $USER->GetUserGroupArray();
        $bAllowAccess = (count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)? true: false;
        // check whether current user has access to view list
        /*if ($USER->IsAdmin() || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
        {
        $bAllowAccess = true;
        }
        elseif ($USER->GetID() > 0 && $arParams["ELEMENT_ASSOC"] != "N")
        {
        $bAllowAccess = true;
        }
        else
        {
        $bAllowAccess = false;
        }*/

        if (!getCurrentCompanyID()) {
            $bAllowAccess = false;
        }

        // if user has access
        if ($bAllowAccess)
        {
            $arResult["CAN_EDIT"] = $arParams["ALLOW_EDIT"] == "Y" ? "Y" : "N";
            $arResult["CAN_DELETE"] = $arParams["ALLOW_DELETE"] == "Y" ? "Y" : "N";

            if ($USER->GetID())
            {
                $arResult["NO_USER"] = "N";

                // get list of iblock properties and list of iblock property ids
                $rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
                $arIBlockPropertyList = array();
                $arPropertyIDs = array();
                $i = 0;
                while ($arProperty = $rsIBLockPropertyList->GetNext())
                {
                    $arIBlockPropertyList[] = $arProperty;
                    $arPropertyIDs[] = $arProperty["ID"];
                }

                // set starting filter value
                $arFilter = array("IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], "SHOW_NEW" => "Y");
                // check type of user association to iblock elements and add user association to filter
                //echo "<pre>"; print_r($arParams); echo "</pre>";

                /*if ($arParams["ELEMENT_ASSOC"] == "PROPERTY_ID" && intval($arParams["ELEMENT_ASSOC_PROPERTY"]) > 0 && in_array($arParams["ELEMENT_ASSOC_PROPERTY"], $arPropertyIDs))
                {
                $arFilter["PROPERTY_".$arParams["ELEMENT_ASSOC_PROPERTY"]] = $USER->GetID();
                //$arFilter["PROPERTY_".$arParams["ELEMENT_ASSOC_PROPERTY"]] = getCurrentCompanyID();
                }
                else
                {
                $arFilter["CREATED_BY"] = $USER->GetID();
                }*/

                $arFilter["PROPERTY_COMPANY"] = getCurrentCompanyID();

                //echo "<pre>"; print_r($arFilter); echo "</pre>";

                // deleteting element
                if (check_bitrix_sessid() && $_REQUEST["delete"] == "Y" && $arResult["CAN_DELETE"])
                {
                    $arParams["ID"] = intval($_REQUEST["CODE"]);

                    // try to get element with id, for user and for iblock
                    $rsElement = CIBLockElement::GetList(array(), array_merge($arFilter, array("ID" => $arParams["ID"])));
                    if ($arElement = $rsElement->GetNext())
                    {
                        // delete one
                        $DB->StartTransaction();
                        if(!CIBlockElement::Delete($arElement["ID"]))
                        {
                            $DB->Rollback();
                        }
                        else
                        {
                            $DB->Commit();
                        }
                    }
                }

                if ($bWorkflowIncluded)
                {
                    $rsWFStatus = CWorkflowStatus::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"), $is_filtered);
                    $arResult["WF_STATUS"] = array();
                    while ($arStatus = $rsWFStatus->GetNext())
                    {
                        $arResult["WF_STATUS"][$arStatus["ID"]] = $arStatus["TITLE"];
                    }
                }
                else
                {
                    $arResult["ACTIVE_STATUS"] = array("Y" => GetMessage("IBLOCK_FORM_STATUS_ACTIVE"), "N" => GetMessage("IBLOCK_FORM_STATUS_INACTIVE"));
                }

                // get elements list using generated filter
                //echo "<pre>"; print_r($arParams); echo "</pre>";
                //echo "<pre>"; print_r($arFilter); echo "</pre>";
                $arOrder = array(
                    // "PROPERTY_DATE_FROM"=>"ASC",
                    "ID" => "DESC",
                );

                //собираем свойства для текущего элемента
                $arSelectedProperties = array();
                $properties = CIBlock::GetProperties( $arParams["IBLOCK_ID"],Array(),Array());
                while($arProperty = $properties->Fetch()) {
                    $arSelectedProperties[] = $arProperty["CODE"];
                }

                //arshow($arProperties);

                //фильтр



                if ($_REQUEST["setFilter"] == "Y") {

                    // arshow($_GET["filter"]);

                    //добавляем фильтр в сессию
                    $_SESSION["filter"] = $_REQUEST["filter"];
                    $_SESSION["filter"]["URL"] = $APPLICATION->GetCurPage();

                    foreach ($_REQUEST["filter"] as $property_code=>$prop_id) {

                        if ($property_code == "HOTEL" && $APPLICATION->GetCurPage() != "/tours/tour/" && !in_array(0,$prop_id)) {
                            $arFilter["PROPERTY_".$property_code] = $prop_id;
                        }

                        else if ($property_code == "ID" && $prop_id != "")  { 
                            //хитрый хак на случай если в фильтр попадет еще фамилия туриста 
                            $arFilter[">ID"] = intval($prop_id)-1;
                            $arFilter["<ID"] = intval($prop_id)+1;
                        }

                        else if ($property_code == "TOURIST" && strlen($prop_id) > 0) {
                            $arFilter["ID"] = CIBlockElement::SubQuery("PROPERTY_ORDER", array("IBLOCK_CODE" => "TOURIST","%NAME" => $prop_id));
                        }

                        else if ($property_code == "COMPANY_NAME") {
                            $arFilter["%PROPERTY_COMPANY_NAME"] = $prop_id; 
                        } 

                        else if ($property_code == "NAME") {
                            $arFilter["%NAME"] = $prop_id; 
                        }                           

                        else if ($property_code == "IS_DISCOUNT") {
                            $arFilter[">PROPERTY_DISCONT"] = "0"; 
                        }  

                        else if ($property_code == "TYPE_BOOKING") {
                            $arFilter["PROPERTY_TYPE_BOOKING"] = $prop_id;
                        }

                        else if ($property_code == "STATUS") {
                            $arFilter["PROPERTY_STATUS"] = $prop_id;
                        }
                        
                        else if ($property_code == "TOUR") {
                            $arFilter["PROPERTY_TOUR"] = $prop_id;
                        }


                        else if ($property_code != "DATE_FROM" && $property_code != "SPECIAL_OFFER" && $property_code != "DATE_CREATE" && $property_code != "ID" ){

                            foreach ($prop_id as $id=>$val) {
                                if ($id != 0){
                                    $arFilter["PROPERTY_".$property_code][] = $id;   //направление и др
                                }

                                //для текущего направления собираем города
                                if ($property_code == "DIRECTION" && !$_REQUEST["filter"]["CITY"]){
                                    $cities = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"CITY","PROPERTY_DIRECTION"=>$id, "PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array());
                                    while($arCity = $cities->Fetch()) {
                                        $arFilter["PROPERTY_CITY"][] = $arCity["ID"];   
                                    }
                                }
                            }
                        } 

                    }

                    //дата начала тура ОТ
                    if ($_GET["filter"]["DATE_FROM"]["FROM"]) {
                        $date_from = explode(".",$_GET["filter"]["DATE_FROM"]["FROM"]); 
                        $arFilter[">=PROPERTY_DATE_FROM"] = $date_from[2]."-".$date_from[1]."-".$date_from[0]; //date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$date_from[1],$date_from[0],$date_from[2])); 

                    }
                    //дата начала тура ДО
                    if ($_GET["filter"]["DATE_FROM"]["TO"]) {
                        $date_to = explode(".",$_GET["filter"]["DATE_FROM"]["TO"]);
                        $arFilter["<=PROPERTY_DATE_FROM"] = $date_to[2]."-".$date_to[1]."-".$date_to[0]." 00:00:00"; //date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$date_to[1],$date_to[0],$date_to[2])); 
                    }

                    //дата создания ОТ
                    if ($_GET["filter"]["DATE_CREATE"]["FROM"]) {
                        $date_create_from = explode(".",$_GET["filter"]["DATE_CREATE"]["FROM"]); 
                        $arFilter[">=DATE_CREATE"] = date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), mktime(0,0,0,$date_create_from[1],$date_create_from[0],$date_create_from[2]));
                    }
                    //дата создания ДО
                    if ($_GET["filter"]["DATE_CREATE"]["TO"]) {
                        $date_create_to = explode(".",$_GET["filter"]["DATE_CREATE"]["TO"]);
                        $arFilter["<=DATE_CREATE"] = date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), mktime(23,59,00,$date_create_to[1],$date_create_to[0],$date_create_to[2]));

                    }   

                    //спецпредложение
                    if ($_GET["filter"]["SPECIAL_OFFER"]) {
                        $arFilter["PROPERTY_SPECIAL_OFFER_VALUE"] = "Да";
                    }   



                }




                //проверяем инфоблок. если выводится инфоблок заказов (или туристов), то для турагенств нужно добавить фильтр по создателю

                $iblock = CIBlock::GetById($arParams["IBLOCK_ID"]);
                $arIblock = $iblock->Fetch();
                //получаем группы пользователя
                $userGroups = getUserGroup($USER->GetID()); 

                if (!in_array("TOUR_OPERATOR",$userGroups) && in_array("TOUR_AGENCY",$userGroups) && ($arIblock["CODE"] == "ORDERS" || $arIblock["CODE"] == "TOURIST")) {
                    $arFilter["CREATED_BY"] = $USER->GetId();
                } 

                //по умолчанию для заказов удаляем из общего списка аннулированные
                //получаем ID значения статуса "аннулирован"
                $propertyCancelled = CIBlockPropertyEnum::GetList(array(), Array("CODE"=>"STATUS","XML_ID"=>"STATUS_CANCELLED"));
                $arPropertyCancelled = $propertyCancelled->Fetch();
                if ($arIblock["CODE"] == "ORDERS" && !$_GET["filter"]["SHOW_CANCELLED"] == "Y" && $arFilter["PROPERTY_STATUS"] != $arPropertyCancelled["ID"]) {  
                    $arFilter["!PROPERTY_STATUS"] = $arPropertyCancelled["ID"]; 
                }
                ///   


                //получаем количество дней для помещения заказа в архив
                $companyProps = getCompanyProperties();
                

                //получаем метку времени для фильтрации
                $cur_day_label = date("U");
                $filter_date_label = $cur_day_label - $companyProps["DAYS_COUNT_FOR_ARCHIVE"]["VALUE"]*86400;
                $filter_date = date("Y-m-d 00:00:00",$filter_date_label);


                //для архива добавляем фильтр по дате   
                if (in_array("orders_archive",explode("/",$APPLICATION->GetCurPage())))
                {
                    $arFilter["<=PROPERTY_DATE_FROM"] = $filter_date;
                }

                else {    
                    //добавляем фильтр по дате отправления
                    $arFilter[">PROPERTY_DATE_FROM"] = $filter_date;
                }      


                //arshow($arParams);
                //arshow($_GET);
                //arshow($arFilter);     



                
                
                //////////////////////////////
                $rsIBlockElements = CIBlockElement::GetList($arOrder, $arFilter);

                $arResult["ELEMENTS_COUNT"] = $rsIBlockElements->SelectedRowsCount();
                //$page_split = intval(COption::GetOptionString("iblock", "RESULTS_PAGEN"));
                $arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
                $arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;


                $rsIBlockElements->NavStart($arParams["NAV_ON_PAGE"]);

                // get paging to component result
                if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
                {
                    $arResult["NAV_STRING"] = $rsIBlockElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
                }


                // get current page elements to component result
                $arResult["ELEMENTS"] = array();
                $bCanEdit = false;
                $bCanDelete = false;
                while ($arElement = $rsIBlockElements->NavNext(false))
                {
                    $arElement = htmlspecialcharsex($arElement);
                    if ($bWorkflowIncluded)
                    {
                        $PREVIOUS_ID = $arElement['ID'];
                        $LAST_ID = CIBlockElement::WF_GetLast($arElement['ID']);
                        if ($LAST_ID != $arElement["ID"])
                        {
                            $rsElement = CIBlockElement::GetByID($LAST_ID);
                            $arElement = $rsElement->GetNext();
                        }
                        $arElement["ID"] = $PREVIOUS_ID;

                        $arElement["CAN_EDIT"] = $arResult["CAN_EDIT"] == "Y" ? (in_array($arElement["WF_STATUS_ID"], $arParams["STATUS"]) == true ? "Y" : "N") : "N";
                        $arElement["CAN_DELETE"] = $arResult["CAN_DELETE"] == "Y" ? (in_array($arElement["WF_STATUS_ID"], $arParams["STATUS"]) == true ? "Y" : "N") : "N";
                    }
                    elseif (in_array("INACTIVE", $arParams["STATUS"]) === true)
                    {
                        $arElement["CAN_EDIT"] = $arResult["CAN_EDIT"] == "Y" ? ($arElement["ACTIVE"] == "Y" ? "N" : "Y") : "N";
                        $arElement["CAN_DELETE"] = $arResult["CAN_DELETE"] == "Y" ? ($arElement["ACTIVE"] == "Y" ? "N" : "Y") : "N";
                    }
                    else
                    {
                        $arElement["CAN_EDIT"] = $arResult["CAN_EDIT"];
                        $arElement["CAN_DELETE"] = $arResult["CAN_DELETE"];
                    }

                    if (!$bCanEdit && $arResult["CAN_EDIT"] == "Y" && $arElement["CAN_EDIT"] == "Y")
                    {
                        $bCanEdit = true;
                    }

                    if (!$bCanDelete && $arResult["CAN_DELETE"] == "Y" && $arElement["CAN_DELETE"] == "Y")
                    {
                        $bCanDelete = true;
                    } 

                    $arElement["PROPERTIES"] = array();
                    //получаем свойства элемента
                    $elementProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arElement["ID"], Array(), Array("CODE"=>$arSelectedProperties));
                    while($arElementProperty = $elementProperties->Fetch()) {
                        //arshow($arElementProperty);
                        $arElementProperty["DISPLAY_VALUE"] = $arElementProperty["VALUE"];

                        //для каждого свойства оставляем возможность множественности 
                        if (!is_array($arElement["PROPERTIES"][$arElementProperty["CODE"]])) {
                            $arElement["PROPERTIES"][$arElementProperty["CODE"]] = $arElementProperty; 
                        }

                        if ($arElementProperty["PROPERTY_TYPE"] == "E" && $arElementProperty["NAME"] != "COMPANY") { //тип свойства - привязка к элементам
                            if (!is_array($arElement["PROPERTIES"][$arElementProperty["CODE"]]["VALUE"])) {
                                $arElement["PROPERTIES"][$arElementProperty["CODE"]] = $arElementProperty;
                                $arElement["PROPERTIES"][$arElementProperty["CODE"]]["VALUE"] = array();
                                $arElement["PROPERTIES"][$arElementProperty["CODE"]]["VALUE"][] = $arElementProperty["VALUE"]; 
                            }
                            else {

                                $arElement["PROPERTIES"][$arElementProperty["CODE"]]["VALUE"][] = $arElementProperty["VALUE"]; 
                            } 

                        }                        

                        // $arElement["PROPERTIES"][$arElementProperty["CODE"]] = $arElementProperty;
                    }


                    $arResult["ELEMENTS"][] = $arElement;

                }

                if ($arResult["CAN_EDIT"] == "Y" && !$bCanEdit) $arResult["CAN_EDIT"] = "N";
                if ($arResult["CAN_DELETE"] == "Y" && !$bCanDelete) $arResult["CAN_DELETE"] = "N";


            }
            else
            {
                $arResult["NO_USER"] = "Y";
            }

            //__dump($arResult["ELEMENTS"]);
            $arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);

            $this->IncludeComponentTemplate();
        }
        else
        {
            $APPLICATION->AuthForm("");
        }

        //если в урле есть параметр удаления элемента и для текущей страницы существует фильтр, то надо восстановить фильтр
        if ($_REQUEST["delete"]="Y" && intval($_REQUEST["CODE"]) > 0 && is_array($_SESSION["filter"]) && $_SESSION["filter"]["URL"] == $APPLICATION->GetCurPage()) {
            $new_url = $APPLICATION->GetCurPage()."?setFilter=Y&";
            // arshow($_SESSION["filter"]);
            foreach ($_SESSION["filter"] as $param=>$param_id) {
                if ($param != "URL") {
                    foreach ($param_id as $id=>$val){
                        $new_url .= "filter[".$param."][".$id."]"."=".$val."&";   
                    }
                }
            } 
            $new_url = substr($new_url,0,strlen($new_url) - 1);
            header("location: ".$new_url) ;
        }                                  
    }
?>