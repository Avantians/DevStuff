<?php
/** -----------------------------------------
 * Class for Database connection.
 */
namespace myFolder\allFiles;

use PDO;

class getPDO {

  private $_DB;
  private $_DB_HOST;
  private $_DB_USERNAME;
  private $_DB_PASSWORD;
  private $_DB_DATABASE_NAME;

  public function __construct( $dbParams )
  {
    //Assign the host name if passed in
    if ( strlen( trim( $dbParams['DB_SERVER'] ) ) > 0 ) {
      $this->setHost( $dbParams['DB_SERVER'] );
    }
    //Assign the user name if passed in
    if ( strlen( trim( $dbParams['DB_USERNAME'] ) ) > 0 ) {
      $this->setUsername( $dbParams['DB_USERNAME'] );
    }
    //Assign the password if passed in
    if ( strlen( trim( $dbParams['DB_PASSWORD'] ) ) > 0 ) {
      $this->setPassword( $dbParams['DB_PASSWORD'] );
    }
    //Assign the database name if passed in
    if ( strlen( trim( $dbParams['DB_NAME'] ) ) > 0 ) {
      $this->setDatabaseName( $dbParams['DB_NAME'] );
    }

  }

  /**
   * initialization HOST
   */
  public function setHost( $host )
  {
    $this->_DB_HOST = $host;
  }

  /**
   * initialization USERNAME
   */
  public function setUsername( $user )
  {
    $this->_DB_USERNAME = $user;
  }

  /**
   * initialization PASSWORD
   */
  public function setPassword( $password )
  {
    $this->_DB_PASSWORD = $password;
  }

  /**
   * initialization DATABASE Name
   */
  public function setDatabaseName( $name )
  {
    $this->_DB_DATABASE_NAME = $name;
  }

  /**
   * open Connection to Database
   */
  public function getOpen()
  {
    //Check if the connection is not already set
    if ( isset( $this->_DB ) ) {
      return;
    }

    //Make sure that the host, the username, the password, and the database name are set
    if ( ( !isset( $this->_DB_HOST ) ) || ( strlen( $this->_DB_HOST ) == 0 )
      || ( !isset( $this->_DB_USERNAME ) ) || ( strlen( $this->_DB_USERNAME ) == 0 )
      || ( !isset( $this->_DB_PASSWORD ) ) || ( strlen( $this->_DB_PASSWORD ) == 0 )
      || ( !isset( $this->_DB_DATABASE_NAME ) ) || ( strlen( $this->_DB_DATABASE_NAME ) == 0 )
      ){
      throw new Exception( 'DATABASE VARIABLES HAVE NOT BEEN SET' );
    }

    try {
        $this->_DB = new PDO('mysql:host='. $this->_DB_HOST .';dbname='. $this->_DB_DATABASE_NAME .'', $this->_DB_USERNAME, $this->_DB_PASSWORD);
        $this->_DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Force MySQL to use the UTF-8 character set. Also set the collation, if a certain one has been set;
        // otherwise, MySQL defaults to 'utf8_general_ci' for UTF-8.
        $this->_DB->exec('SET NAMES utf8 COLLATE utf8_general_ci');
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }

    return $this->_DB;
  }

  /**
   * close Database connection
   */
  public function getClose()
  {
    if ( isset( $this->_DB ) ) {
      $this->_DB = null;
    }
  }
}

/** -----------------------------------------
 * Class for get color table and number of vote for color.
 */
class getColorVoted {

  protected $getPDO;
  protected $_db;
  protected $table_colum;
  protected $sum;
  protected $stmt;
  protected $tableFrame;

  public function __construct( getPDO $getPDO )
  {
    $this->_db = $getPDO->getOpen();
    $this->table_colum = null;
    $this->sum = 0;
  }

  /**
   * get number of vote for color.
   *
   * @param string $trigger as color
   * @return integer as sum
   */
  public function getVotes ( $trigger = null )
  {
    // put trigger as WHERE condition
    if ( !empty ($trigger) ) {
      $sql_tail =  ' WHERE Votes.color = \''. $trigger .'\'';
    }

    $sql_statment = 'SELECT Colors.color, Votes.city, Votes.votes FROM Colors LEFT JOIN Votes ON Colors.color = Votes.color'. $sql_tail .' ORDER BY Votes.votes DESC';
    $result = $this->getDbResult( $sql_statment );
    foreach( $result as $row){
      $this->sum += $row['votes'];
    }

    return $this->sum;
  }

  /**
   * To get list of color as table
   *
   * @return strings
   */
  public function getTableList ()
  {
    $colorSql_statment = 'SELECT color FROM Colors ORDER BY id';
    $result = $this->getDbResult( $colorSql_statment );
    $count = 1;
    foreach( $result as $row){
        $this->table_colum .= '<tr>
                                <td scope="row" class="hiddenitem">'.$count.'</th>
                                <td><a onfocus="this.blur()" title="How many voted" href="#" data-name="'.$row['color'].'">'.$row['color'].'</a></td>
                                <td><span id="'.$row['color'].'" class="voted"></span></td>
                              </tr>';
        $count++;
    }

    return str_replace('%%CONTENT%%', $this->table_colum, $this->getTableFram());
  }

  /**
   * PDO basic function
   *
   * @param string $sql_statment
   * @return strings | integer
   */
  public function getDbResult ( $sql )
  {
    $this->stmt = $this->_db->prepare( $sql );
    $this->stmt->execute();

    return $this->stmt->fetchAll( PDO::FETCH_ASSOC );
  }

  /**
   * Table frame
   *
   * @return strings
   */
  public function getTableFram ()
  {
    $this->tableFrame = <<<EOD
<table class="table table-striped">
<thead>
  <tr>
    <th class="number hiddenitem">#</th>
    <th class="colorName">Colors</th>
    <th class="colorVote">Votes</th>
  </tr>
</thead>
<tbody>
%%CONTENT%%
  <tr>
    <th scope="row" class="hiddenitem"> </th>
    <td><a onfocus="this.blur()" title="Total number of vote" href="#" data-name="total"><strong>TOTAL</strong></a></td>
    <td><span id="total"></span></td>
  </tr>
</tbody>
</table>
EOD;

    return $this->tableFrame;
  }
}
