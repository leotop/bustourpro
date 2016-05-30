<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("тест");
?>

<?
    $res = mysql_query("select * from briz_users");
    while ($arRes = mysql_fetch_assoc($res)) {
       // arshow($arRes);
        //$salt = randString(8);$arFields["PASSWORD"] = $salt.md5($salt.$arFields["PASSWORD"]); 

        /*

        [id] => 1
        [name] => Хавина Екатерина
        [login] => ka3n
        [password] => ka3n
        [email] => ka3n@rambler.ru
        [phone] => 36-15-15
        [agency] => Бриз (Екатерина Х.)
        [id_user_city] => 4
        [discount] => 0
        [is_active] => 1
        [id_user_group] => 9
        [direction] => null

        */
        
        if (strlen($arRes["password"]) < 6) {
            $arRes["password"] .=$arRes["password"]; 
        }

        $user = new CUser;
        $fields = Array(
            "NAME" => $arRes["agency"],
            "EMAIL" => $arRes["email"],
            "LOGIN" => $arRes["login"], 
            "SECOND_NAME" => $arRes["name"],
            "PASSWORD" => $arRes["password"],
            "CONFIRM_PASSWORD" => $arRes["password"], 
            "PERSONAL_PHONE" => $arRes["phone"],
            "UF_COMPANY_ID"  => 6959, 
            "ACTIVE" => "N",            
            "GROUP_ID" => array(7),    
        );  
        
        if ($arRes["is_active"] == 1) {
          $fields["ACTIVE"] = "Y";  
        }
        
        if ($arRes["discount"] > 0) {
           $fields["UF_COMPANY_DISCOUNT"] = $arRes["discount"]; 
        }
        
       /*  
        if ($user->add($fields)) {
            echo "Пользователь добавлен: ".$arRes["login"]."<br>";
        } else {
            echo "Пользователь не добавлен добавлен: ".$arRes["login"]." ".$user->LAST_ERROR."<br>";     
        }
         */
    }
?>


 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>