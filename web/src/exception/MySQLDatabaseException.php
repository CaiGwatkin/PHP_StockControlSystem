<?php
/*
 * Gwatkin, 15146508
 */

namespace cgwatkin\a3\exception;

/**
 * Class MySQLDatabaseException
 *
 * Thrown when MySQL database cannot be loaded.
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class MySQLDatabaseException extends \Exception
{
    /**
     * NoMySQLException constructor.
     *
     * @param string $message The exception message.
     * @param int $code The code of the exception.
     */
    public function NoMySQLException($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}