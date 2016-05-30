<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //получаем заказы на тур
    if (intval($_POST["tour_id"]) > 0) {
        $ID = $_POST["tour_id"];
        $ordersID = "";

        //получаем список статусов заказов. нам нужно выбрать только неаннулированные
        $iblock = CIBlock::GetList(array(),array("CODE"=>"ORDERS"));
        $arIblock = $iblock->Fetch(); 
        //получаем ID статуса "заказ аннулирован"                                                      
        $props = CIBlockPropertyEnum::GetList(array(), Array("IBLOCK_ID"=>$arIblock["ID"],"CODE"=>"STATUS","XML_ID"=>"STATUS_CANCELLED"));
        $arProps = $props->Fetch(); 
        //выбираем только не аннулированные заказы                                                            
        $orders = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ORDERS","PROPERTY_TOUR"=>$ID,"!PROPERTY_STATUS"=>$arProps["ID"]), false, false, array("ID"));
        $i=0;
        while($arOrder = $orders->Fetch()) {
            if ($i > 0){
                $ordersID .= ",";   
            }
            $ordersID .= $arOrder["ID"];
            $i++;
        }  

        //$orders = json_encode($ordersID);

        echo $ordersID; 
    }
?>