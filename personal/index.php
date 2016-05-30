<?    if ($_GET["auth"] == "yes") {
        define("NEED_AUTH", true);
    }
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Профиль пользователя");
?>

<?    

    if ($_GET["register"] == "yes") {
        // echo checkNotice();
        // arshow(getCompanyProperties());
    ?>


    <?
        if ($_POST["add_user"] == "Y" && is_array($_POST["user"])) {
            $msg = ""; 
            $user = new CUser;
            $fields = Array(
                "NAME" => $_POST["user"]["NAME"],
                "EMAIL" => $_POST["user"]["EMAIL"],
                "LOGIN" => $_POST["user"]["LOGIN"], 
                "SECOND_NAME" => $_POST["user"]["SECOND_NAME"],
                "PASSWORD" => $_POST["user"]["PASSWORD"],
                "CONFIRM_PASSWORD" => $_POST["user"]["CONFIRM_PASSWORD"], 
                "UF_COMPANY_CITY" => $_POST["user"]["COMPANY_CITY"],
                "PERSONAL_PHONE" => $_POST["user"]["PERSONAL_PHONE"],
                "UF_COMPANY_ID"  => $GLOBALS["DEFAULT_COMPANY_ID"],
                "ACTIVE" => "N",
                "GROUP_ID" => array(7),    
            );   
            if ($user->add($fields)) {
                $msg = "Спасибо! Ваша заявка принята";


                $props = getCompanyProperties();
                //отправляем письмо, если у туроператора отмечена данная опция
                if (checkNotice() == "Y") {
                    //формируем данные для письма


                    //письмо ОПЕРАТОРУ
                    $THEME = "В системе онлайн бронирования BUSTOURPRO зарегистрировалась новая компания"; 
                    $TEXT = "<h3>Данные о компании</h3>
                    <p>
                    Название компании: <b>".$_POST["user"]["NAME"]."</b><br>
                    ФИО менеджера: <b>".$_POST["user"]["SECOND_NAME"]."</b><br>
                    Логин: <b>".$_POST["user"]["LOGIN"]."</b><br>
                    Email: <b>".$_POST["user"]["EMAIL"]."</b><br>
                    Город: <b>".$_POST["user"]["COMPANY_CITY"]."</b><br>
                    Телефон: <b>".$_POST["user"]["PERSONAL_PHONE"]."</b><br>
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

                //письмо АГЕНТСТВУ
                $THEME = "Регистрация в системе онлайн бронирования BUSTOURPRO";
                $TEXT = "<h3>Вы зарегистрировадись в системе онлайн бронирования BUSTOURPRO</h3>
                <p>Ваши данные: 
                Название компании: <b>".$_POST["user"]["NAME"]."</b><br>
                ФИО менеджера: <b>".$_POST["user"]["SECOND_NAME"]."</b><br>
                Логин: <b>".$_POST["user"]["LOGIN"]."</b><br>
                Email: <b>".$_POST["user"]["EMAIL"]."</b><br>
                Город: <b>".$_POST["user"]["COMPANY_CITY"]."</b><br>
                Телефон: <b>".$_POST["user"]["PERSONAL_PHONE"]."</b><br>
                </p>
                <p>После проверки данных, ваша учетная запись будет активирована и вы сможете работать в системе</p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $_POST["user"]["EMAIL"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );      
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");


            }
            else {
                $msg = $user->LAST_ERROR;      
            }    

            echo "<p><font class='notetext'>".$msg."</font></p>";      
        }
    ?>
    <form method="post" action="" name="user_edit">
        <h2>Регистрация</h2>
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
                <td>Город</td>
                <td><input type="text" name="user[COMPANY_CITY]" value=""></td>
            </tr>

            <tr>
                <td>Телефон</td>
                <td><input type="text" name="user[PERSONAL_PHONE]" value=""></td>
            </tr>  


        </table>
        <br>  
        <input type="hidden" name="add_user" value="Y">
        <input type="submit" value="Сохранить" class="btn btn-success">

    </form> 

    <br>
    <a href="/personal/?auth=yes">авторизация</a>
    <?}

    else {?>
    <?$APPLICATION->IncludeComponent("bitrix:main.profile", "company_profile", array(
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"SET_TITLE" => "Y",
	"USER_PROPERTY" => array(
		0 => "UF_COMPANY_CITY",
		1 => "UF_FULL_NAME",
		2 => "UF_COMPANY_ADRESS",
		3 => "UF_FACT_ADRESS",
		4 => "UF_INN",
		5 => "UF_KPP",
		6 => "UF_BILL",
		7 => "UF_BANK",
		8 => "UF_KOR_BILL",
		9 => "UF_BIK",
		10 => "UF_COMPANY_PHONE",
		11 => "UF_COMPANY_EMAIL",
		12 => "UF_DIRECTOR",
		13 => "UF_FOUNDATION",
		14 => "UF_COMPANY_OKPO",
		15 => "UF_COMPANY_VNT",
		16 => "UF_OGRN",
	),
	"SEND_INFO" => "N",
	"CHECK_RIGHTS" => "N",
	"USER_PROPERTY_NAME" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>        
    <?}

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>