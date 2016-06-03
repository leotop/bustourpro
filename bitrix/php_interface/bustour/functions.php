<?php

/**
 * Возврощает idCompany текущего авторизованного пользователя. Иначе возвращает NULL.
 *
 * @return null|int
 */
function getCurrentCompanyID() {
    global $USER;

    $company_id = null;
    if ($USER->IsAuthorized()) {
        $rsUser = $USER->GetByID($USER->GetID())->Fetch();
        if ($rsUser) {
            $company_id = $rsUser['UF_COMPANY_ID'];
        }
    }        
    
    if (!$company_id) {$company_id = $GLOBALS["DEFAULT_COMPANY_ID"];}

    return $company_id;
}

//получаем название туристической компании к которой относится текущий пользователь
function getCurrentCompanyName() {
    $company_id = getCurrentCompanyID();
    $name = CIBlockElement::getById($company_id);
    $arName = $name->Fetch();
    
    return $arName["NAME"];
}

/**
 * Debug данных
 *
 * @param $var
 * @param bool $exit
 */
function arshow($var, $exit = false) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';

    if ($exit) {
        die;
    }
}

class sfVException extends Exception {}

/**
 * Class sfForm
 * Вспомогательный класс для сохранения/получения данных посредством сессий. Нужен при работе с обработкой форм.
 */
class sfForm {

    public static function setSFK($key, $value) {
        $key = md5($key);
        $_SESSION['fs_message'][$key] = serialize($value);
    }

    public static function getSFK($key) {
        $key = md5($key);
        $value = null;

        if (isset($_SESSION['fs_message']) && isset($_SESSION['fs_message'][$key])) {
            $value = unserialize($_SESSION['fs_message'][$key]);
            unset($_SESSION['fs_message'][$key]);
        }

        return $value;
    }
}

/**
 * Склонение числительных
 *
 * @param $number
 * @param $titles
 * @return mixed
 */
function declension_of_numerals($number, $titles) {
    $cases = array (2, 0, 1, 1, 1, 2);
    return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}