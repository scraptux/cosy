<?php

$dirs = ['database','enum','request','response'];
foreach ($dirs as $dir) {
	$d = scandir($dir);
	foreach ( $d as $file ){
    	if ( $file != '.' && $file != '..' ){
        	if ( strlen($file)>=5 ) {
            	if ( substr($file, -4) == '.php' ) {
	                	include_once __DIR__.'/'.$dir.'/'.$file;
	            }    
	        }
	    }
	}
}

?>