<?php

namespace modules\Database;

use modules\Oci8\Oci8Connection;
use modules\Oci8\Oci8Statement;


class oradb extends AbstractDatabase
  {
  /**
   * @param String $userName
   * @param String $password
   * @param String $SID
   * @param String $host
   * @param String|null $applicationName
   * @param Int $port
   * @return bool
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function connect(String $userName,
                                 String $password,
                                 String $SID,
                                 String $host,
                                 String $applicationName = null,
                                 Int $port = 1521)
    {
    $connectionString = '//' . $host . ':' . $port . '/' . $SID;
    //TODO add more params including encoding
    //TODO add try/catch around connection
    $dbInstance = new Oci8Connection($userName, $password, $connectionString);

    self::addInstance(DBTypes::ORACLE, $dbInstance);

    if ($applicationName) {
      self::setApplicationName($applicationName);
    }

    return true;
    }

  /**
   * @param String $sql
   * @return Oci8Statement
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function prepare(String $sql): Oci8Statement
    {
    return self::getInstance()->parse($sql);
    }

  /**
   * @param       $statement
   * @param array $params
   * @return Oci8Statement
   */
  protected static function bind($statement, array $params): Oci8Statement
    {
    // move to statement bind
    foreach ($params as $paramName => $paramValue) {
      if (is_a($paramValue, '\OCI-Collection')) {
        $statement->bindByName($paramName, $paramValue, -1, SQLT_NTY);
      } elseif (!is_array($paramValue)) {
        $statement->bindByName($paramName, $paramValue);
      } else {
        $statement->bindArrayByName($paramName, $paramValue, -1);
      }
    }
    return $statement;
    }

  /**
   * @param String $sql
   * @param array $params
   * @return Oci8Statement
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function execute(String $sql, array $params = [], $mode = null): ?Oci8Statement
    {
    $statement = self::getInstance()->parse($sql);
    self::bind($statement, $params);
    return $statement->execute($mode) ? $statement : null;
    }

  /**
   * @param String $sql
   * @param array $params
   * @return array
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function getAll(String $sql, array $params = []): array
    {
    $data = [];

    $statement = self::execute($sql, $params);
    $rowsCount = $statement->fetchAll($data);
    $statement->free();

    return $data;
    }

  /**
   * @param String $sql
   * @param array $params
   * @return array
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function getOne(String $sql, array $params = [])
    {
    $statement = self::execute($sql, $params);
    return $statement->fetchAssoc();
    }


  public static function transactionStart() : bool
    {
    return self::getInstance()->transactionStart();
    }

  /**
   * @return bool
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function commit(): bool
    {
    return self::getInstance()->commit();
    }

  /**
   * @return bool
   * @throws \modules\Oci8\Oci8Exception
   */
  public static function rollback(): bool
    {
    return self::getInstance()->rollback();
    }

  public static function getCursor()
    {
    // TODO: Implement getCursor() method
    }

  protected static function getInstance(String $dbType = DBTypes::ORACLE): Oci8Connection
    {
    return parent::getInstance($dbType);
    }

  protected static function setApplicationName(String $applicationName)
    {
    //TODO switch to module name?
    self::getInstance()->setClientInfo($applicationName);
    }
  //TODO add getNewCollection method
  }