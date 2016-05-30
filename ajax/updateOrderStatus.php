<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    if ($_POST["new_status"] && $_POST["status_id"] && $_POST["new_status_name"] && $_POST["order_id"]){

        //проверка текущего статуса заказа
        $orderCheck = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=> "ORDERS", "ID"=>$_POST["order_id"]),false,false,array("PROPERTY_STATUS"))->Fetch();

        global $DB;        
        $DB->query("UPDATE `b_iblock_element_property` set value='".intval($_POST["new_status"])."', value_enum='".intval($_POST["new_status"])."' where id='".intval($_POST["status_id"])."'");

        //если заказ аннулируется, то нужно освободить места в автобусе и номер         
        if ($_POST["new_status_name"] == "STATUS_CANCELLED") {  

            //если заказ еще не аннулирован
            if($orderCheck["PROPERTY_STATUS_ENUM_ID"] != intval($_POST["new_status"])) {
                
                itemsDelete($_POST["order_id"]);

                eventLogAdd("BUSTOURPRO_EVENT_ORDER_CANCELLED",$_POST["order_id"],"Заказ ".$_POST["order_id"]." аннулирован");        

                //отправка письма

                $props = getCompanyProperties();  

                $order = CIBlockElement::GetById($_POST["order_id"]);
                $arOrder = $order->Fetch();                                                                

                $userData = CUser::GetById($arOrder["CREATED_BY"]);
                $arUserData = $userData->Fetch();

                //письмо АГЕНТСТВУ
                $THEME = "Ваш заказ в системе онлайн бронирования BUSTOURPRO аннулирован"; 
                $TEXT = "<h3>Данные о заказе</h3>
                <p>
                № заказа: <b>".$_POST["order_id"]."</b><br>
                Статус заказа: <b>Заказ аннулирован</b><br>
                </p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $arUserData["EMAIL"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );                                  
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
                //    }
                ///////////////////////
            }
        }


        //если делается запрос на аннуляцию        
        if ($_POST["new_status_name"] == "STATUS_FOR_CANCEL") {
            //отправка письма
            if (checkNotice() == "Y") {
                //формируем данные для письма
                $props = getCompanyProperties();                   

                $userData = CUser::GetById($USER->GetID());
                $arUserData = $userData->Fetch();

                //письмо ОПЕРАТОРУ
                $THEME = "Запрос на аннуляцию заказа в системе онлайн бронирования BUSTOURPRO"; 
                $TEXT = "<h3>Данные о заказе</h3>
                <p>
                № заказа: <b>".$_POST["order_id"]."</b><br>
                Статус заказа: <b>Запрос на аннуляцию</b><br>
                Компания: <b>".$arUserData["NAME"]."</b>                   
                </p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $props["EMAIL"]["VALUE"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );                                  
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
            }
            ///////////////////////
        }




        //если заказ подтвержден        
        if ($_POST["new_status_name"] == "STATUS_ACCEPTED") {
            //отправка письма
            //     if (checkNotice() == "Y") {
            //формируем данные для письма
            $props = getCompanyProperties();  

            $order = CIBlockElement::GetById($_POST["order_id"]);
            $arOrder = $order->Fetch();                                                                

            $userData = CUser::GetById($arOrder["CREATED_BY"]);
            $arUserData = $userData->Fetch();

            //письмо ОПЕРАТОРУ
            $THEME = "Ваш заказ №".$_POST["order_id"]." в системе онлайн бронирования BUSTOURPRO одобрен"; 
            $TEXT = "<h3>Данные о заказе</h3>
            <p>
            № заказа: <b>".$_POST["order_id"]."</b><br>
            Статус заказа: <b>Заказ одобрен</b><br>
            </p>
            "; 
            $emailData = array(
                "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                "EMAIL" => $arUserData["EMAIL"],
                "THEME" => $THEME,
                "TEXT" => $TEXT
            );                                  
            CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
            //    }
            ///////////////////////
        }





    }
?>