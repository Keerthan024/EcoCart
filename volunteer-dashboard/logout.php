<?php
session_start();
if (!isset($_SESSION['id'])||!isset($_SESSION['mobile'])||!isset($_SESSION['email'])) {
    header("Location: ../login"); 
    exit();
}else{
            session_unset();
            session_destroy();
            header("Location: ../login");
            exit;
 }
?>
