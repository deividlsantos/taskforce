<?php

namespace Source\Boot;

use PDO;
use PDOException;

class SqlServerConn
{
    private static $conn;

    // ✅ Defina aqui as credenciais do SQL Server
    private static $host = "ll1b6VnLldEWDRDRlV0RkdGRyNWcY5yYDFWN";   // ex: 192.168.0.10 ou NOME_DO_SERVIDOR
    private static $port = "TN3cTVGN";           // padrão, altere se necessário
    private static $db   = "=MFSDpUUWJXQYh0UTh0X0pnU";
    private static $user = "=QnQlhXM";
    private static $pass = "lhjRWlnZDBUND1GW0lVSlhjb";
    private static $dbalias = "=YUSyd2NYZFRyRDVyRzXDRFT";

    public static function connect(): ?PDO
    {
        if (!self::$conn) {
            try {

                if (ENV_TEST) {
                    // 🔹 DSN usando ODBC + FreeTDS (Linux ARM)
                    $dsn = "odbc:Driver=/usr/local/freetds/lib/libtdsodbc.so;"
                        . "Server=" . ll_decode(self::$host) . ";"
                        . "Port=" . ll_decode(self::$port) . ";"
                        . "Database=" . ll_decode(self::$db) . ";"
                        . "TDS_Version=7.3;";
                } elseif (defined('FREETDS_ENABLED') && FREETDS_ENABLED) {
                    $dsn = "dblib:host=" . ll_decode(self::$dbalias) . ";dbname=" . ll_decode(self::$db);
                } else {
                    $dsn = "sqlsrv:Server=" . ll_decode(self::$host) . "," . ll_decode(self::$port) . ";Database=" . ll_decode(self::$db);
                }

                self::$conn = new PDO($dsn, ll_decode(self::$user), ll_decode(self::$pass), [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]);
            } catch (PDOException $e) {
                return null;
            }
        }

        return self::$conn;
    }
}
