<?php

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;
    private static int $maxRetries = 5;
    private static int $retryDelay = 2;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $retries = 0;
            $lastException = null;
            $host = getenv('DB_HOST');
            $port = getenv('DB_PORT');
            $dbname = getenv('DB_NAME');
            $username = getenv('DB_USERNAME');
            $password = getenv('DB_PASSWORD');

            while ($retries < self::$maxRetries) {
                try {
                    self::$connection = new PDO(
                        "mysql:host=$host;port=$port;dbname=$dbname",
                        $username,
                        $password,
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );

                    break;

                } catch (PDOException $e) {
                    $lastException = $e;
                    if ($e->getCode() === 1049) {
                        try {
                            $tempConnection = new PDO(
                                "mysql:host=$host;port=$port",
                                $username,
                                $password
                            );

                            $tempConnection->exec("CREATE DATABASE IF NOT EXISTS $dbname");
                            $tempConnection = null;

                            self::$connection = new PDO(
                                "mysql:host=$host;port=$port;dbname=$dbname",
                                $username,
                                $password,
                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                            );
                            break;
                        } catch (PDOException $e) {
                            $lastException = $e;
                        }
                    }

                    $retries++;
                    if ($retries < self::$maxRetries) {
                        sleep(self::$retryDelay);
                    }
                }
            }

            if (self::$connection === null) {
                throw new PDOException(
                    "Connection failed after " . self::$maxRetries . " attempts. Last error: " . 
                    ($lastException ? $lastException->getMessage() : 'Unknown error')
                );
            }
        }

        return self::$connection;
    }
} 