<?php
namespace ElasticActs\Constants\models;

use ElasticActs\Constants\libraries\getHandleElements as getElt;
use ElasticActs\Constants\models\getDB;
use PDO;
/**
 * Class for Database connection.
 */
class getStmt extends getDB
{

    public static function getStatment( $loadValue )
    {
// ['table'      => ['tableName'],
// 'fields'     => ['field1', 'field2', 'field3', 'field3'],
// 'joins'      => ['tableName', 'field One', '=', 'tableName.field Tow']
// 'conditions' => ['where' => ['valuable', ‘LIKE‘, 'value'],
//                  'where' => ['valuable', ‘LIKE‘, 'value'],
//                  'orWhere' => ['valuable', ‘LIKE‘, 'value'],
//                  'orWhere' => ['valuable', ‘LIKE‘, 'value']
//                 ],
// 'order'      => [ 'orderFiled', 'orderType']
// 'limit'      => [ 'first', 'last']]
        $list = getStmt::getTempQuery([
                                        'table'      => ['tableName'],
                                        'fields'     => ['field1', 'field2', 'field3', 'field4'],
                                        'joins'      => ['tableName', 'field One', '=', 'tableName.field Tow'],
                                        'conditions' => [
                                                             'where'   => ['valuable', ‘LIKE‘, 'value'],
                                                             'where'   => ['valuable', ‘LIKE‘, 'value'],
                                                             'orWhere' => ['valuable', ‘LIKE‘, 'value'],
                                                             'orWhere' => ['valuable', ‘LIKE‘, 'value']
                                                        ],
                                        'order'      => ['orderFiled', 'orderType'],
                                        'limit'      => ['first', 'last']
                                      ]);

        $tst = getElt::checkNullValue( $val );
        var_dump($tst);
        //print_r( $loadValue );

        getDB::getQuery( "show status like '%Slow%'" );
        $ch = getDB::getExcuteFetchAll();
        var_dump($ch);
        //$tssst = getDB::getExcuteFetchAll();
        //var_dump($tssst);

        //getDB::truncateTB( 'staff' );
    }

/**
$item_list = $this->dbConnect->getAllContents( array(
                                                 'table'      => array( $section_items['tbname'], 'opensef' ),
                                                   'joins'      => array( 'joinTable' => 'menu', 'joinField' => array( 'menu.pid' => 'opensef.pid')),
                                                   'conditions' => array( 'opensef.tbname', 'LIKE',
                                                                          'opensef.tbname = \''. $section_items['tbname'].'\'',
                                                                          $section_items['tbname'].'.id = opensef.tid',
                                                                          $section_items['tbname'].'.publish = 1',
                                                                          $section_items['tbname'].'.status = 1',
                                                                          $section_items['tbname'].'.notice = 0',
                                                                          $section_items['tbname'].'.group_level >= '. $this->GeneralItems->setAccesslevel('gid'),
                                                                          $section_items['tbname'].'.access_level >= '. $this->GeneralItems->setAccesslevel('ulevel') ),
                                                   'orConditions' => $list_tail,
                                                   'order'        => [
                                                                        'orderField' => $section_items['tbname'].'.ordering',
                                                                        'orderType' => 'desc'
                                                                     ],
                                                   'limit'        => [$first, CONFIG_HOW_MANY_ARTICLES_PER_PAGE]
                                                   ),
                                                   'ALL');

$list = getDB::getTempQuery(
                            'table'      => [tableName],
                            'fields'     => [field1, field2, field3, field3],
                            'joins'      => ['tableName', 'field One', '=', 'tableName.field Tow']
                            'conditions' => ['where' => ['valuable', ‘LIKE‘, 'value'],
                                             'where' => ['valuable', ‘LIKE‘, 'value'],
                                             'orWhere' => ['valuable', ‘LIKE‘, 'value'],
                                             'orWhere' => ['valuable', ‘LIKE‘, 'value']
                                            ],
                            'order'      => [ 'orderFiled', 'orderType']
                            'limit'      => [ $first, $last]
                            );
 */

   public static function getTempQuery( $fileds = null )
    {
        foreach( $fileds as $key => $value) {
            echo $key .'-'. $value.'<br>';
            if ( is_array($value) ) {
                foreach( $value as $key2 => $value2 ) {
                    echo '-'.$key2 .'::'. $value2.'<br>';
                    if ( is_array($value2) ){
                        foreach( $value2 as $key3 => $value3 ) {
                            echo '--'.$key3 .'::'. $value3.'<br>';
                        }
                    }

                }
            }
            echo "<br/>";
        }
    }

    public static function getAllContents( $queryOpt, $all = 'SINGLE', $type = null )
    {
        $db_query = $this->renderStmt('SELECT', ['fields'  => $this->setFields( $queryOpt['fields'] ),
                                                 'table'   => $this->setTable( $queryOpt['table'] ),
                                                 'joins'   => $this->setJoin( $queryOpt['joins'], $queryOpt['table'] ),
                                                 'where'   => $this->setWhere( $queryOpt['conditions'] ),
                                                 'orWhere' => $this->setOrWhere( $queryOpt['orConditions'] ),
                                                 'group'   => $this->setGroup( $queryOpt['group'] ),
                                                 'order'   => $this->setOrder( $queryOpt['order'] ),
                                                 'limit'   => $this->setLimit( $queryOpt['limit'] )
                                                ]);

        $binding_info = !empty( $queryOpt['orConditions']) ? array_merge($queryOpt['conditions'], $queryOpt['orConditions']) : $queryOpt['conditions'];
        $this->getQuery( $db_query );
        $this->getBindProcess( $binding_info );

        if( $all === 'ALL' ){
            return $this->getResultAll( $type );
        } else {
            return $this->getSingle( $type );
        }
    }

    protected static function renderStmt( $type, $data )
    {
        extract($data);

        switch (strtolower($type)) {
            case 'select':
                return trim("SELECT {$fields} FROM {$table} {$joins} {$conditions} {$orConditions} {$group} {$order} {$limit}");
                break;
            case 'create':
                return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
                break;
            case 'update':
                return trim("UPDATE {$table} SET {$fields} {$conditions}");
                break;
            case 'delete':
                return trim("DELETE {$alias} FROM {$table} {$aliases}{$conditions}");
                break;
        }
    }

    protected function setFields( $fileds = null )
    {
        if( is_null($fileds) ) {
            return '*';
        } else {
            foreach( $fileds as $key => $value){
                $new[] = ( is_numeric( $key ) ) ? $value : $key .' = '. $value;
            }
            return implode(', ', $new);
        }
    }

    protected function setTable( $tables )
    {
        if( is_array($tables) && getElt::checkNullValue($tables) ) {
            return implode(', ', $tables);
        } else {
            return $tables;
        }
    }

    protected function setJoin( $joinFileds = null, $mainTable )
    {
        if( !getElt::checkNullValue($joinFileds) ) {
            return $this->join( $joinFileds, $mainTable, true);
        }
    }

    protected function setWhere( $orFileds = null )
    {
        if( !getElt::checkNullValue($orFileds) ) {
            return $this->orConditions( $orFileds );
        }
    }

    protected function getGroup( $groupFileds = null )
    {
        if( !getElt::checkNullValue( $groupFileds ) ) {
            return $this->group( $groupFileds );
        }
    }

    protected function getOrder( $orderFileds = null )
    {
        if( !getElt::checkNullValue( $orderFileds ) ) {
            extract($orderFileds);
            return $this->order( $orderField, $orderType );
        }
    }

    protected function getLimit( $limitNumber = null )
    {
        if( !getElt::checkNullValue( $limitNumber ) ) {
            return $this->limit( $limitNumber );
        }
    }

    protected function getBindProcess( $bindItems )
    {
        foreach( $bindItems as $key => $value) {
            if( count(explode( "#", $key )) == 1 ) {
                if(is_string($key)) {
                    $this->getBind( $key, $value );
                }
            } else {
                $oprator = explode( '#', $key );
                $new_key = str_replace( "#$oprator[1]", '', $key );
                $this->getBind( $new_key, $value );
            }
        }
    }

    protected function conditions( $conditions, $where = true)
    {
        if( $where ) {
            $clause = 'WHERE ';
        }

        if( is_array($conditions) && !getElt::checkNullValue($conditions) ) {
            foreach ($conditions as $key => $value) {
                $new_conditions[] = ( is_numeric( $key ) ) ? $value : $key .' = :'. $key;
            }

            return $clause . implode( ' AND ', $new_conditions );
        }
    }

    // protected function orConditions( $conditions )
    // {
    //     if ( is_array( $conditions ) && !getElt::checkNullValue( $conditions ) ) {
    //         foreach ( $conditions as $key => $value ) {
    //             if ( count(explode( '#', $key )) > 1 ) {
    //                 $opr = explode( '#', $key );
    //                 $new_key = str_replace( "#$opr[1]", '', $key );
    //                 if ( is_numeric( $key ) ) {
    //                     $new_conditions[] = $opr[1] === '%'  ? $new_key ." LIKE $new_key" : $new_key ." $opr[1] ". $new_key;
    //                 } else {
    //                     $new_conditions[] = $opr[1] === '%'  ? $new_key ." LIKE :$new_key" : $new_key ." $opr[1] :". $new_key;
    //                 }
    //             } else {
    //                 $new_conditions[] = ( is_numeric( $key ) ) ? key($conditions) .' = '. $value : $key .' = :'. $key;

    //             }
    //         }

    //         return " AND ( ". implode( ' OR ', $new_conditions ) ." )";
    //     }
    // }

    protected function join( $conditions, $mainTable, $operator = true )
    {
        $out = '';
        if ( $operator ) {
            $joint =  'LEFT JOIN ';
        }

        if ( is_array( $conditions ) && !getElt::checkNullValue( $conditions) ) {
                if ( !getElt::checkNullValue( $conditions['joinTable'] ) && getElt::checkNullValue( $out) ) {
                    $out = $conditions['joinTable'] .' ON ';
                }

                foreach ( $conditions['joinField'] as $key => $value ) {
                    $out .= $key .' = '. $value;
                }
        }

        return $joint . $out;
    }

    // $conditions = array( 'filedName' => 'value' )
    protected function order( $conditions, $type = 'asc' ){
            $clause = ' ORDER BY ';
            $typeTail = ( $type === 'desc') ? ' DESC' : '';

            if ( is_array( $conditions ) && !getElt::checkNullValue( $conditions ) ) {
                return $clause . implode(', ', $conditions) . $typeTail;
            } elseif ( is_string( $conditions) && !getElt::checkNullValue( $conditions )) {
                return $clause . $conditions . $typeTail;
            }

    }

    protected function group( $conditions )
    {
            $clause = ' GROUP BY ';

            if ( is_array($conditions) && !getElt::checkNullValue($conditions) ) {
                return $clause . implode(', ', $conditions);
            }
    }

    protected function limit( $offset ){
            $clause = ' LIMIT ';

            if( is_array($offset) && !getElt::checkNullValue($offset) ){
                return $clause . implode(', ', $offset);
            }
    }
}
