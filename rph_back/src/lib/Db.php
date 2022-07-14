<?php

/**
 * PDO MySQL database class.
 *
 * Use this statically to access database.
 * Define Db credentials in `config/config.inc`.
 *
 * https://phpdelusions.net/pdo
 */

class Db
{
    const CHARSET = 'utf8mb4';

    protected static $_host;
    protected static $_dbname;
    protected static $_username;
    protected static $_password;

    protected static $_pdo;

    protected static $_statement;

    protected static $_error;

    public static function init()
    {
        static::$_host = Config::get("mysql_host");
        static::$_dbname = Config::get("mysql_dbname");
        static::$_username = Config::get("mysql_username");
        static::$_password = Config::get("mysql_password");

        $host = Config::get("mysql_host");
        $dbname = Config::get("mysql_dbname");
        $username = Config::get("mysql_username");
        $password = Config::get("mysql_password");

        $dsn = "mysql:
            host=" . $host . ";
            dbname=" . $dbname . ";
            charset=" . self::CHARSET
        ;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
             static::$_pdo = new PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
             throw new \PDOException($e->getMessage(), (int)$e->getCode());
            show_error('PDO Connection error');
        }
    }

    // Keep in mind that every value is returned as string.
    public static function execute(string $sql, array $params = [], $fetch_mode = PDO::FETCH_ASSOC)
    {
        try {
            static::$_statement = static::$_pdo->prepare($sql);
            static::$_statement->execute($params);
        } catch (Exception $e) {

            if (Config::get('is_dev_environment', false)) {
                throw $e;
            }

            respond_error('Database error.');
        }
        $data = static::$_statement->fetchAll($fetch_mode);

        return $data;
    }

    public static function select_and_group_by_first_param(array $params, string $table)
    {
        return array_map('reset', static::$_pdo->query("SELECT " . implode(',', $params) . " FROM " . $table)->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
    }

    public static function last_insert_id()
    {
        return static::$_pdo->lastInsertId();
    }

    public static function get_error()
    {
        if (isset(static::$_error)) {
            return static::$_error;
        }

        return '';
    }
}

?>
