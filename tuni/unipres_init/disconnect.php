<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("config/auth.ini.php");
require_once("config/main.ini.php");

session_start();
session_unset();
session_destroy();

header("Location: ".CAS_BASE."/logout?service=".INDEX_PAGE);

?>
