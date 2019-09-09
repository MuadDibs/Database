<?php

namespace modules\Database;

abstract class AbstractDatabase
  {
  private static $instances = [];
  
  abstract protected static function connect(String $userName,
                                             String $password,
                                             String $dbname,
                                             String $host,
                                             String $applicationName,
                                             Int $port);
  
  abstract public static function prepare(String $sql);
  
  abstract protected static function bind($statement, Array $params);
  
  abstract public static function execute(String $sql, Array $params = []);
  
  abstract public static function getAll(String $sql, Array $params = []);
  
  abstract public static function getOne(String $sql, Array $params = []);
  
  abstract public static function commit();
  
  //abstract public static function rollback();

    // check in postgresql, implement in oracle to avoid OCI_NO_AUTO_COMMIT usage
  //abstract public static function transactionStart();

  protected static function getInstance(String $dbType)
    {
    return self::$instances[$dbType][0] ?? null;
    }
  
  protected static function addInstance(String $dbType, $dbInstance)
    {
    if (!empty(self::$instances[$dbType]))
      {
      self::$instances[$dbType][] = $dbInstance;
      }
    else
      {
      self::$instances[$dbType][0] = $dbInstance;
      }
    }
  
  abstract protected static function setApplicationName(String $applicationName);
  }