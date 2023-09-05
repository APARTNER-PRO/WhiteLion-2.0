<?php

if(file_exists(APP_PATH."config.php"))
{
 	header("Location: ".SITE_URL."step2");
 	exit();
}

?>