<?php
/**
 * Created by PhpStorm.
 * User: MuadDib
 * Date: 14.06.2017
 * Time: 5:57
 */

namespace core\pgdb;

class pgdb
  {
  private static $pgConnection;
  private static $queriesCollection = Array();
  private static $preparedQueries = Array();
  
  public static function connect(String $host = PGHostName,
                                 String $port = PGPort,
                                 String $dbname = PGDBName,
                                 String $user = PGUserName,
                                 String $password = PGDBPass)
    {
    //FIXME change consts to method paramethers
    //TODO add autoconect parameter, init method and autoconnect
    //@self::$pgConn=pg_pconnect('host='.PGHostName.
    @self::$pgConnection = pg_connect('host=' . $host .
                                      ' port=' . $port .
                                      ' dbname=' . $dbname .
                                      ' user=' . $user .
                                      ' password=' . $password);
    $stat = pg_connection_status(self::$pgConnection);
    if ($stat === PGSQL_CONNECTION_OK)
      {
      self::getPreparedQueriesList();
      return true;
      }
    else
      {
      return false;
      }
    }
  
  public static function close()
    {
    pg_close(self::$pgConnection);
    }
  
  private static function returnQueryObject($paramsArr)
    {
    if ((sizeof($paramsArr) == 1) || (is_array($paramsArr[1])))
      {
      $queryName = md5($paramsArr[0]) . '-' . crc32($paramsArr[0]);
      $queryText = $paramsArr[0];
      $queryParams = (!empty($paramsArr[1])) ? $paramsArr[1] : Array();
      }
    else
      {
      $queryName = (String)$paramsArr[0];
      $queryText = $paramsArr[1];
      $queryParams = (!empty($paramsArr[2])) ? $paramsArr[2] : Array();
      }
    if (!@self::$queriesCollection[$queryName] instanceof pgQuery)
      {
      self::$queriesCollection[$queryName] = new pgQuery(self::$pgConnection,
                                                         $queryName,
                                                         $queryText,
                                                         $queryParams);
      }
    else
      {
      self::$queriesCollection[$queryName]->setQueryParams($queryParams);
      }
    return self::$queriesCollection[$queryName];
    }
  
  private static function getPreparedQueriesList()
    {
    $result = pg_query(self::$pgConnection, 'select name from pg_prepared_statements');
    while ($data = pg_fetch_object($result))
      {
      self::$preparedQueries[(string)$data->name] = 1;
      }
    }
  
  public static function checkIfPrepared($queryName)
    {
    return (@self::$preparedQueries[(string)$queryName] == 1) ? true : false;
    }
  
  public static function prepareQuery($queryName, $queryText)
    {
    pg_prepare(self::$pgConnection, $queryName, $queryText);
    self::$preparedQueries[(string)$queryName] = 1;
    }
  
  public static function showPrepareds()
    {
    echo '<pre>';
    print_r(self::$preparedQueries);
    echo '</pre>';
    }
  
  /*
  public static function clearPrepared()
    {
    
    }
  */
  public static function prepare(): pgQuery
    {
    return self::returnQueryObject(func_get_args())->prepare();
    }
  
  public static function getAll()
    {
    return self::returnQueryObject(func_get_args())->prepare()->execute()->getAll();
    }
  
  public static function getOne()
    {
    return self::returnQueryObject(func_get_args())->prepare()->execute()->getOne();
    }
  
  public static function execute()
    {
    return self::returnQueryObject(func_get_args())->prepare()->execute();
    }
  }