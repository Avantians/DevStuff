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
 * HTML for color table.
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Master view</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <style>
    th.number { width: 4%; }
    th.colorName { width: 48%; }
    th.colorVote { width: 48%; }
    .hiddenitem { display:none; }
    </style>
  </head>
  <body>
        <div class="container">
            <div class="content">
<?php
$dic = new getPDO( $dbParams );
$getColorsTable = new getColorVoted( $dic );
echo $getColorsTable->getTableList();
?>
            </div>
        </div>
  <script type="text/javascript" src="allFiles/_allJquery.js"></script>
  </body>
</html>
