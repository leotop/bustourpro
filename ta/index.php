<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Туристические агентства");
?>

<?if ($_GET["edit"] == "Y" && intval($_GET["CODE"])> 0) {?>
    <?
        //сохранение пользователя
        if ($_POST["save_user"] == "Y" && is_array($_POST["user"])){  

            //проверяем статус пользователя перед сохранением
            $user = CUser::GetById($_POST["user"]["ID"]);
            $arUser = $user->Fetch();

            $active = "N";
            if($_POST["user"]["ACTIVE"] == "on") {
                $active = "Y";
            }

            $is_operator = 0;
            if($_POST["user"]["UF_IS_OPERATOR"] == 1) {
                $is_operator = 1;
            }
            

            $msg = ""; 
            $user = new CUser;
            $fields = Array(
                "NAME" => $_POST["user"]["NAME"],
                "SECOND_NAME" => $_POST["user"]["SECOND_NAME"],
                "EMAIL" => $_POST["user"]["EMAIL"],
                "LOGIN" => $_POST["user"]["LOGIN"], 
                "PERSONAL_PHONE" => $_POST["user"]["PERSONAL_PHONE"],
                "UF_COMPANY_DISCOUNT" => $_POST["user"]["UF_COMPANY_DISCOUNT"],
                "UF_COMPANY_CITY" => $_POST["user"]["UF_COMPANY_CITY"],
                "UF_IS_OPERATOR" => $is_operator,
                "ACTIVE" => $active     
            );   
            if ($user->Update($_POST["user"]["ID"], $fields)) {
                $msg = "Изменения успешно сохранены"; 

                $props = getCompanyProperties();
                //деактивируем пользователя
                if ($arUser["ACTIVE"] == "Y" && $active == "N") {
                    //письмо АГЕНТСТВУ
                    $THEME = "Ваша учетная запись в системе онлайн бронирования BUSTOURPRO заблокирована";
                    $TEXT = "<h3>Ваша учетная запись в системе онлайн бронирования BUSTOURPRO была заблокирована</h3>
                    <p>За подробностями обратитесь к туроператору</p>
                    "; 
                    $emailData = array(
                        "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                        "EMAIL" => $_POST["user"]["EMAIL"],
                        "THEME" => $THEME,
                        "TEXT" => $TEXT
                    );      
                    CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");  
                }

                //активируем пользователя
                if ($arUser["ACTIVE"] == "N" && $active == "Y") {
                       //письмо АГЕНТСТВУ
                    $THEME = "Активация учетной записи в системе онлайн бронирования BUSTOURPRO";
                    $TEXT = "<h3>Ваша учетная запись активирована, вы можете пользоваться системой</h3>
                    <p>
                    Ваши данные:<br>
                    Логин: <b>".$_POST["user"]["LOGIN"]."</b><br>
                    Название компании: <b>".$_POST["user"]["NAME"]."</b><br>
                    ФИО менеджера: <b>".$_POST["user"]["SECOND_NAME"]."</b><br>
                    Телефон: <b>".$_POST["user"]["PERSONAL_PHONE"]."</b><br>
                    Город: <b>".$_POST["user"]["UF_COMPANY_CITY"]."</b><br>
                    <br>
                    За дополнительной информацией обратитесь к туроператору</p>
                    "; 
                    $emailData = array(
                        "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                        "EMAIL" => $_POST["user"]["EMAIL"],
                        "THEME" => $THEME,
                        "TEXT" => $TEXT
                    );      
                    CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");  
                }


            ?>
            <script>
                $(function(){
                    window.top.location.reload()
                })
            </script>
            <?}
            else {
                $msg = $user->LAST_ERROR; 
            }    

            echo "<p><font class='notetext'>".$msg."</font></p>"; 
        } 
        //добавление пользователя
    ?>

    <form method="post" action="" name="user_edit">
        <input type="hidden" name="user[ID]" value="<?=$_GET["CODE"]?>">
        <?
            $user = CUser::GetById($_GET["CODE"]);
            $arUser = $user->Fetch();
            //arshow($arUser);
        ?>
        <h2>Редактирование компании <?=$arUser["NAME"]?></h2>   
        <table class="data-table">   

            <tr>
                <td>Активен</td>
                <td><input type="checkbox" name="user[ACTIVE]" <?if ($arUser["ACTIVE"] == "Y"){?> checked="checked"<?}?>></td>
            </tr>      

            <tr>
                <td>Название компании</td>
                <td><input type="text" name="user[NAME]" value='<?=$arUser["NAME"]?>'></td>
            </tr>

            <tr>
                <td>ФИО менеджера</td>
                <td><input type="text" name="user[SECOND_NAME]" value='<?=$arUser["SECOND_NAME"]?>'></td>
            </tr>

            <tr>
                <td>Логин</td>
                <td><input type="text" name="user[LOGIN]" value='<?=$arUser["LOGIN"]?>'></td>
            </tr>

            <tr>
                <td>Email</td>
                <td><input type="text" name="user[EMAIL]" value='<?=$arUser["EMAIL"]?>'></td>
            </tr>

            <tr>
                <td>Скидка, %</td>
                <td><input type="text" name="user[UF_COMPANY_DISCOUNT]" value="<?=$arUser["UF_COMPANY_DISCOUNT"]?>"></td>
            </tr>   

            <tr>
                <td>Город</td>
                <td><input type="text" name="user[UF_COMPANY_CITY]" value="<?=$arUser["UF_COMPANY_CITY"]?>"></td>
            </tr>

            <tr>
                <td>Телефон</td>
                <td><input type="text" name="user[PERSONAL_PHONE]" value="<?=$arUser["PERSONAL_PHONE"]?>"></td>
            </tr>

            <tr>
                <td>Менеджер туроперетора</td>
                <td><input type="checkbox" name="user[UF_IS_OPERATOR]" <?if ($arUser["UF_IS_OPERATOR"] == 1){?> checked="checked"<?}?> value="1"></td>
            </tr>

        </table>
        <br>
        <input type="hidden" name="save_user" value="Y">
        <input type="submit" value="Сохранить" class="btn btn-success">

    </form> 
    <br>
    <a href="javascript:void(0)" onclick="window.top.location.reload()">Назад к списку</a>
    <?} 


    //форма добавления пользователя
    else if ($_GET["edit"] == "Y" && intval($_GET["CODE"])== 0) {?>
    <?

        if ($_POST["add_user"] == "Y" && is_array($_POST["user"])) {
            $msg = ""; 
            $user = new CUser;

            if ($_POST["user"]["UF_IS_OPERATOR"] != 1) {
                $_POST["user"]["UF_IS_OPERATOR"] = 0;
            }

            $fields = Array(
                "ACTIVE" => "Y",
                "NAME" => $_POST["user"]["NAME"],
                "SECOND_NAME" => $_POST["user"]["NAME"],
                "EMAIL" => $_POST["user"]["EMAIL"],
                "LOGIN" => $_POST["user"]["LOGIN"], 
                "PERSONAL_PHONE" => $_POST["user"]["PERSONAL_PHONE"],
                "UF_COMPANY_DISCOUNT" => $_POST["user"]["UF_COMPANY_DISCOUNT"],
                "UF_COMPANY_CITY" => $_POST["user"]["UF_COMPANY_CITY"],
                "UF_IS_OPERATOR" => $_POST["user"]["UF_IS_OPERATOR"],
                "PASSWORD" => $_POST["user"]["PASSWORD"],
                "CONFIRM_PASSWORD" => $_POST["user"]["CONFIRM_PASSWORD"], 
                "UF_COMPANY_ID"  => getCurrentCompanyID(),
                "GROUP_ID" => array(7)    
            );   
            if ($user->add($fields)) {
                $msg = "Пользователь успешно добавлен";
            }
            else {
                $msg = $user->LAST_ERROR; 
            }    

            echo "<p><font class='notetext'>".$msg."</font></p>"; 


        }
    ?>
    <form method="post" action="" name="user_edit">
        <h2>Добавление компании</h2>
        <table class="data-table">             

            <tr>
                <td>Название компании</td>
                <td><input type="text" name="user[NAME]" value=""></td>
            </tr>

            <tr>
                <td>ФИО менеджера</td>
                <td><input type="text" name="user[SECOND_NAME]" value=""></td>
            </tr>

            <tr>
                <td>Логин</td>
                <td><input type="text" name="user[LOGIN]" value="" autocomplete="off"></td>
            </tr>

            <tr>
                <td>Пароль</td>
                <td><input type="password" name="user[PASSWORD]" value=""></td>
            </tr>

            <tr>
                <td>Подтверждение пароля</td>
                <td><input type="password" name="user[CONFIRM_PASSWORD]" value=""></td>
            </tr>       

            <tr>
                <td>Email</td>
                <td><input type="text" name="user[EMAIL]" value=""></td>
            </tr>

            <tr>
                <td>Скидка, %</td>
                <td><input type="text" name="user[UF_COMPANY_DISCOUNT]" value=""></td>
            </tr>


            <tr>
                <td>Город</td>
                <td><input type="text" name="user[UF_COMPANY_CITY]" value=""></td>
            </tr> 

            <tr>
                <td>Телефон</td>
                <td><input type="text" name="user[PERSONAL_PHONE]" value=""></td>
            </tr>    

            <tr>
                <td>Менеджер туроперетора</td>
                <td><input type="checkbox" name="user[UF_IS_OPERATOR]" value="1"></td>
            </tr>

        </table>
        <br>
        <input type="hidden" name="add_user" value="Y">
        <input type="submit" value="Сохранить" class="btn btn-success">

    </form> 
    <br>
    <a href="javascript:void(0)" onclick="window.top.location.reload()">Назад к списку</a>



    <?} 


    //удаление пользователя
    else if($_GET["delete"] == "Y" && intval($_GET["CODE"]) > 0){
        $user_to_delete = CUser::GetById($_GET["CODE"]);
        $arUser_to_delete = $user_to_delete->Fetch();
        if($arUser_to_delete["UF_COMPANY_ID"] == getCurrentCompanyID()) {//если пользователь относится к текущему туроператору
            CUser::Delete($_GET["CODE"]);
            header("location: ".$APPLICATION->GetCurPage());
        }
        else {
            header("location: ".$APPLICATION->GetCurPage());
        }
    } else{?>
    <table class="data-table">
        <tr>
            <td colspan="11"><a class="fancybox" href="?edit=Y&CODE=0">Добавить</a></td>  
        </tr>
        <tr>
            <td><b>Активность</b></td>
            <td><b>Название компании</b></td>
            <td><b>ФИО менеджера</b></td>
            <td><b>Логин</b></td>
            <td><b>Email</b></td>
            <td><b>Дополнительная скидка, %</b></td>
            <td><b>Город</b></td>
            <td><b>Телефон</b></td>
            <td><b>Менеджер туроператора</b></td>
            <td colspan="2"></td>
        </tr>

        <?
            $users = CUser::GetList(($by="id"), ($order="asc"),array("UF_COMPANY_ID"=>getCurrentCompanyID(),"GROUPS_ID"=>7), array("FIELDS"=>array("LOGIN","EMAIL","NAME", "SECOND_NAME","ID","ACTIVE" ,"PERSONAL_PHONE"),"SELECT"=>array("UF_COMPANY_ID","UF_COMPANY_DISCOUNT", "UF_COMPANY_CITY","UF_IS_OPERATOR")));
            while ($arUser = $users->Fetch()) {?>
            <tr <?if ($arUser["ACTIVE"] == "N"){?> style="background:#FF8789"<?}?>>
                <td><?if ($arUser["ACTIVE"] == "Y"){?>Да<?} else {?>Нет<?}?></td>
                <td><?=$arUser["NAME"]?></td>
                <td><?=$arUser["SECOND_NAME"]?></td>
                <td><?=$arUser["LOGIN"]?></td>
                <td><?=$arUser["EMAIL"]?></td>
                <td><?=$arUser["UF_COMPANY_DISCOUNT"]?></td>
                <td><?=$arUser["UF_COMPANY_CITY"]?></td>
                <td><?=$arUser["PERSONAL_PHONE"]?></td>
                <td <?if ($arUser["UF_IS_OPERATOR"] == 1){?>style="background:#7FEF7F"<?}?>><?if ($arUser["UF_IS_OPERATOR"] == 1){ echo "Да";} else {echo "нет";}?></td>
                <td><a class="fancybox" href="?edit=Y&CODE=<?=$arUser["ID"]?>">редактировать</a></td>
                <td><a href="?delete=Y&CODE=<?=$arUser["ID"]?>" onclick="return confirm('Вы действительно хотите удалить турагентство?')">удалить</a></td>
            </tr>
            <?}
        ?>

    </table> 
    <?}?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>