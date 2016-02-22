<?php
namespace myFolder\allFiles;
/**
 * basicl required files
 */
require_once( "allFiles/_config.php" );
require_once( "allFiles/_allClasses.php" );

use myFolder\allFiles\getPDO;
use myFolder\allFiles\getColorVoted;
/**
 * This file is to return numbers of voted for color.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dic = new getPDO( $dbParams );
$getColorsTable = new getColorVoted( $dic );
echo number_format($getColorsTable->getVotes( $_GET['trigger'] ));
