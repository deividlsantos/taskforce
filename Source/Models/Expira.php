<?php 

namespace Source\Models;

use Source\Boot\SqlServerConn;
use PDO;

class Expira
{
    public static function buscarInativas()
    {
        $conn = SqlServerConn::connect();

        $sql = "
            SELECT CNPJ, Razao, ultexec
            FROM TCEmp
            WHERE Status = 'ATIVO'
            AND ISDATE(ultexec) = 1
            AND (
                -- Calcula dias úteis entre ultexec e hoje
                (DATEDIFF(day, CONVERT(datetime, ultexec, 103), GETDATE())
                 - (DATEDIFF(week, CONVERT(datetime, ultexec, 103), GETDATE()) * 2)
                 - CASE WHEN DATENAME(weekday, CONVERT(datetime, ultexec, 103)) = 'Sunday' THEN 1 ELSE 0 END
                 - CASE WHEN DATENAME(weekday, GETDATE()) = 'Saturday' THEN 1 ELSE 0 END
                ) >= 2
            )
        ";

        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}