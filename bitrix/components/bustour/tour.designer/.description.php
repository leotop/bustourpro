<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Формирование туров",
	"DESCRIPTION" => "",
	"ICON" => "/images/eaddlist.gif",
	"PATH" => array(
		"ID" => "bustour",

		"CHILD" => array(
			"ID" => "bustour_tour_create",
            "NAME" => "Туры"
		),
	),
);
?>