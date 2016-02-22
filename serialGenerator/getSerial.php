<?php
namespace serialGenerator;

/**
 * basicl required files
 */
require_once( "allFiles/numMaker.php" );
require_once( "allFiles/serial.php" );
require_once( "allFiles/validation.php" );

use serialGenerator\allFiles\serial;
use serialGenerator\allFiles\validation;

$val = new validation();
$numberObj = new serial();

if (!empty($_POST['sn'])){
    echo $val->getValidation($_POST['email'], $_POST['sn']);
} else {
    if (empty($_POST['email'])){
        echo "<div class=\"alert alert-danger\" role=\"alert\">If you would like to get your token, please type your email address.</div>";
    } else {
        echo $numberObj->setSerial($_POST['email']);
    }
}

