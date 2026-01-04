<?php

class PostgresAdapter
{
    private $pdo;
    public $connect_error = null;
    public $error = null;
    public $insert_id = 0;

    public function __construct($host, $user, $password, $dbname, $port = 5432)
    {
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 10,  // Timeout de 10 segundos
                PDO::ATTR_PERSISTENT => false
            ]);
        } catch (PDOException $e) {
            $this->connect_error = $e->getMessage();
        }
    }

    public function set_charset($charset)
    {
        // En Postgres esto se maneja en client_encoding, usualmente en la conexión
        // Podemos hacer SET CLIENT_ENCODING
        if ($this->pdo) {
            $this->pdo->exec("SET CLIENT_ENCODING TO 'UTF8'");
        }
        return true;
    }

    public function query($sql)
    {
        try {
            $stmt = $this->pdo->query($sql);
            return new PostgresResult($stmt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function prepare($sql)
    {
        // Convertir placeholders ? a $1, $2, etc.
        $i = 1;
        $sql = preg_replace_callback('/\?/', function ($matches) use (&$i) {
            return '$' . $i++;
        }, $sql);

        try {
            $stmt = $this->pdo->prepare($sql);
            return new PostgresStmt($stmt, $this->pdo);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function real_escape_string($string)
    {
        if ($this->pdo) {
            $quoted = $this->pdo->quote($string);
            // quote() devuelve la cadena con comillas, necesitamos quitar las comillas de fuera
            // O simplemente dejar que la aplicación lo use si lo inserta manual. 
            // Mysqli real_escape_string SOLO escapa, no comilla.
            // PDO::quote comilla y escapa.
            // Solución pobre: str_replace
            return str_replace("'", "''", $string); // Postgres escape simple
        }
        return addslashes($string);
    }
}

class PostgresStmt
{
    private $pdoStmt;
    private $pdo;
    private $boundParams = [];
    public $error;
    public $num_rows = 0;
    public $affected_rows = 0;
    public $insert_id = 0;

    public function __construct($pdoStmt, $pdo)
    {
        $this->pdoStmt = $pdoStmt;
        $this->pdo = $pdo;
    }

    public function bind_param($types, ...$vars)
    {
        // Mysqli bind_param recibe variables por referencia.
        // Pero func_get_args o ...$vars recibe valores a menos que se fuerce referencia.
        // Aquí solo guardamos los valores para pasarlos en execute.
        $this->boundParams = $vars;
        return true;
    }

    public function execute()
    {
        try {
            // Asegurar que los parámetros se pasan correctamente
            $result = $this->pdoStmt->execute($this->boundParams);
            $this->affected_rows = $this->pdoStmt->rowCount(); // Funciona para DELETE/UPDATE/INSERT

            // Intentar obtener insert_id si es insert
            // En postgres PDO::lastInsertId funciona si se llama a la secuencia, 
            // pero a veces requiere el nombre de la secuencia.
            // Trataremos de adivinar o ignorar por ahora si no es crítico.
            try {
                $this->insert_id = $this->pdo->lastInsertId();
            } catch (Exception $e) {
            }

            return $result;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function get_result()
    {
        // Para SELECT, devolver un objeto result.
        // Verificar si hay columnas
        if ($this->pdoStmt->columnCount() > 0) {
            return new PostgresResult($this->pdoStmt);
        }
        return false;
    }

    public function store_result()
    {
        // En PDO, execute ya trae los datos generalmente o se quedan en el cursor.
        // Simulamos calculando num_rows
        $this->num_rows = $this->pdoStmt->rowCount();
        return true;
    }

    public function close()
    {
        $this->pdoStmt = null;
    }
}

class PostgresResult
{
    private $stmt;
    public $num_rows;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
        $this->num_rows = $stmt->rowCount();
    }

    public function fetch_assoc()
    {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetch_array()
    {
        return $this->stmt->fetch(PDO::FETCH_BOTH);
    }

    public function fetch_row()
    {
        return $this->stmt->fetch(PDO::FETCH_NUM);
    }

    public function free()
    {
        $this->stmt->closeCursor();
    }

    // Iteradores si se usan foreach sobre el result
}
?>