<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(    
    "PARAMETERS" => Array(
        "TICKET_ID" => Array(
            "NAME" => GetMessage("TICKET_ID"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => $REQUEST['ID'],
            "COLS" => 10,
        ),

    )   
);