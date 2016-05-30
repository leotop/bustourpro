<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Город для забора туристов");
?><?$APPLICATION->IncludeComponent("bustour:iblock.element.add", "transfer_city", Array(
	"IBLOCK_TYPE" => "additional_information",	// Тип инфоблока
	"IBLOCK_ID" => "9",	// Инфоблок
	"NAV_ON_PAGE" => "50",	// Количество элементов на странице
	"USE_CAPTCHA" => "N",	// Использовать CAPTCHA
	"USER_MESSAGE_ADD" => "",	// Сообщение об успешном добавлении
	"USER_MESSAGE_EDIT" => "",	// Сообщение об успешном сохранении
	"DEFAULT_INPUT_SIZE" => "30",	// Размер полей ввода
	"RESIZE_IMAGES" => "Y",	// Использовать настройки инфоблока для обработки изображений
	"PROPERTY_CODES" => array(	// Свойства, выводимые на редактирование
		0 => "NAME",
		1 => "40",
		2 => "169",
	),
	"PROPERTY_CODES_REQUIRED" => array(	// Свойства, обязательные для заполнения
		0 => "NAME",
	),
	"GROUPS" => array(	// Группы пользователей, имеющие право на добавление/редактирование
		0 => "6",
	),
	"STATUS" => "ANY",	// Редактирование возможно
	"STATUS_NEW" => "N",	// Деактивировать элемент после сохранения
	"ALLOW_EDIT" => "Y",	// Разрешать редактирование
	"ALLOW_DELETE" => "Y",	// Разрешать удаление
	"ELEMENT_ASSOC" => "PROPERTY_ID",	// Привязка к пользователю
	"ELEMENT_ASSOC_PROPERTY" => "39",	// по свойству инфоблока -->
	"MAX_USER_ENTRIES" => "100000",	// Ограничить кол-во элементов для одного пользователя
	"MAX_LEVELS" => "100000",	// Ограничить кол-во рубрик, в которые можно добавлять элемент
	"LEVEL_LAST" => "Y",	// Разрешить добавление только на последний уровень рубрикатора
	"MAX_FILE_SIZE" => "0",	// Максимальный размер загружаемых файлов, байт (0 - не ограничивать)
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",	// Использовать упрощенный визуальный редактор для редактирования текста анонса
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",	// Использовать упрощенный визуальный редактор для редактирования подробного текста
	"SEF_MODE" => "Y",	// Включить поддержку ЧПУ
	"SEF_FOLDER" => "/additional-information/city-taking-tourists/",	// Каталог ЧПУ (относительно корня сайта)
	"USE_FILTER" => "N",	// Показывать фильтр
	"AJAX_MODE" => "N",	// Включить режим AJAX
	"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
	"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
	"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
	"CUSTOM_TITLE_NAME" => "",	// * наименование *
	"CUSTOM_TITLE_TAGS" => "",	// * теги *
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",	// * дата начала *
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",	// * дата завершения *
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",	// * раздел инфоблока *
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",	// * текст анонса *
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",	// * картинка анонса *
	"CUSTOM_TITLE_DETAIL_TEXT" => "",	// * подробный текст *
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",	// * подробная картинка *
	"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>