<?php

namespace modules\Database;

class pgdb extends AbstractDatabase
    {
    private static $preparedQueries = [];

    public static function connect(String $userName,
                                   String $password,
                                   String $dbname,
                                   String $host,
                                   String $applicationName = null,
                                   Int $port = 5432)
        {
        //@self::$pgConn=pg_pconnect('host='.PGHostName.
        $dbInstance = pg_connect('host=' . $host .
            ' port=' . $port .
            ' dbname=' . $dbname .
            ' user=' . $userName .
            ' password=' . $password);
        $stat = pg_connection_status($dbInstance);
        if ($stat === PGSQL_CONNECTION_OK) {
            self::addInstance(DBTypes::POSTGRESQL, $dbInstance);

            if ($applicationName) {
                self::setApplicationName($applicationName);
            }

            self::getPreparedQueriesList($dbInstance);
            return true;
        } else {
            return false;
        }
        }

    public static function prepare(String $sql)
        {
        // TODO: Implement prepare() method.
        }

    public static function bind(array $params)
        {
        // TODO: Implement bind() method.
        }

    public static function execute(String $sql, array $params = [])
        {
        // TODO: Implement execute() method.
        }

    public static function getAll(String $sql, array $params = [])
        {
        // TODO: Implement getAll() method.
        }

    public static function getOne(String $sql, array $params = [])
        {
        // TODO: Implement getOne() method.
        }

    private static function getPreparedQueriesList($dbInstance)
        {
        $result = pg_query($dbInstance, 'select name from pg_prepared_statements');
        while ($data = pg_fetch_object($result)) {
            self::$preparedQueries[(string)$data->name] = 1;
        }
        }

    protected static function setApplicationName(String $applicationName)
        {
        self::execute('set application_name = $1', [$applicationName]);
        }
    }