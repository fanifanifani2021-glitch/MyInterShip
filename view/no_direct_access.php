<?php
error_reporting( false );

if ( strpos( $_SERVER['PHP_SELF'] , 'index.php' ) !== NULL && strpos( $_SERVER['PHP_SELF'] , 'index.php' ) === false && str_contains( 'index.php' ,$_SERVER['SCRIPT_NAME'] ) === false ) { 

	echo "direct";


	exit; 

}
?>
