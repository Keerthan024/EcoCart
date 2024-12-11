<?php

if(!defined("ACCESS_DB")){
    header("location: https://admins-authority.swizosoft.com/");
}else{
    define('DB_DSN', 'mysql:host=localhost;dbname=volunteers');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
}

?>