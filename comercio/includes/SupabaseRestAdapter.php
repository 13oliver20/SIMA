<?php

class SupabaseRestAdapter
{
    private $baseUrl;
    private $apiKey;
    public $connect_error = null;
    public $error = null;

    public function __construct($projectUrl, $apiKey)
    {
        $this->baseUrl = rtrim($projectUrl, '/') . '/rest/v1';
        $this->apiKey = $apiKey;

        // Verificar conectividad básica
        try {
            $headers = [
                'apikey: ' . $this->apiKey,
                'Content-Type: application/json'
            ];

            $ch = curl_init($this->baseUrl . '/');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_NOBODY, true); // Solo HEAD request

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $this->connect_error = 'Error de conexión: ' . curl_error($ch);
            } elseif ($httpCode >= 400 && $httpCode != 404) {
                $this->connect_error = 'Error HTTP: ' . $httpCode;
            }

            curl_close($ch);
        } catch (Exception $e) {
            $this->connect_error = $e->getMessage();
        }
    }

    public function set_charset($charset)
    {
        // No aplicable en REST API
        return true;
    }

    public function query($sql)
    {
        // Para queries SQL arbitrarios, usaremos RPC si está disponible
        // Por ahora, retornamos false para queries directos
        $this->error = "Las queries SQL directas no están soportadas en API REST. Use prepare() o métodos específicos.";
        return false;
    }

    public function prepare($sql)
    {
        // Retornar un statement simulado
        return new SupabaseRestStmt($this->baseUrl, $this->apiKey, $sql);
    }

    public function close()
    {
        // No hay conexión persistente que cerrar
        return true;
    }

    public function real_escape_string($string)
    {
        return addslashes($string);
    }

    // Método helper para hacer peticiones REST
    private function request($method, $endpoint, $data = null, $headers = [])
    {
        $url = $this->baseUrl . $endpoint;

        $defaultHeaders = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $this->error = curl_error($ch);
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($httpCode >= 400) {
            $this->error = "HTTP Error $httpCode: " . $response;
            return false;
        }

        return json_decode($response, true);
    }
}

class SupabaseRestStmt
{
    private $baseUrl;
    private $apiKey;
    private $sql;
    private $boundParams = [];
    public $error;
    public $num_rows = 0;
    public $affected_rows = 0;
    public $insert_id = 0;
    private $result = null;

    public function __construct($baseUrl, $apiKey, $sql)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->sql = $sql;
    }

    public function bind_param($types, ...$vars)
    {
        $this->boundParams = $vars;
        return true;
    }

    public function execute()
    {
        // DEBUG: guardar SQL para inspección
        global $SUPABASE_LAST_SQL;

        // Analizar el SQL y ejecutar la petición REST correspondiente
        $sql = $this->sql;
        $SUPABASE_LAST_SQL = ['original' => $sql, 'params' => $this->boundParams];

        // Reemplazar placeholders $1, $2, etc con valores
        $paramIndex = 0;
        $sql = preg_replace_callback('/\$\d+/', function ($matches) use (&$paramIndex) {
            $value = $this->boundParams[$paramIndex++] ?? '';
            return "'" . addslashes($value) . "'";
        }, $sql);

        $SUPABASE_LAST_SQL['processed'] = $sql;

        // Detectar tipo de query
        if (preg_match('/^\s*SELECT/i', $sql)) {
            return $this->executeSelect($sql);
        } elseif (preg_match('/^\s*INSERT/i', $sql)) {
            return $this->executeInsert($sql);
        } elseif (preg_match('/^\s*UPDATE/i', $sql)) {
            return $this->executeUpdate($sql);
        } elseif (preg_match('/^\s*DELETE/i', $sql)) {
            return $this->executeDelete($sql);
        }

        $this->error = "Tipo de query no soportado";
        return false;
    }

    private function executeSelect($sql)
    {
        // DEBUG
        global $SUPABASE_DEBUG;
        $SUPABASE_DEBUG = ['sql' => $sql];

        // Parsear SELECT usando regex más flexible
        if (preg_match('/SELECT\s+(.+?)\s+FROM\s+(\w+)(?:\s+WHERE\s+(.+?))?(?:\s+LIMIT\s+(\d+))?(?:\s*;?\s*)$/is', $sql, $matches)) {
            $table = $matches[2];
            $where = isset($matches[3]) ? trim($matches[3]) : '';
            $limit = isset($matches[4]) ? $matches[4] : null;

            $SUPABASE_DEBUG['matches'] = $matches;
            $SUPABASE_DEBUG['table'] = $table;
            $SUPABASE_DEBUG['where'] = $where;
            $SUPABASE_DEBUG['limit'] = $limit;

            $endpoint = '/' . $table;
            $queryParams = [];

            // Parsear WHERE clause - mejorado para manejar comillas y diferentes formatos
            if ($where) {
                // Remover LIMIT si quedó en el WHERE
                $where = preg_replace('/\s+LIMIT\s+\d+/i', '', $where);

                // Buscar patrones como: columna = 'valor' o columna='valor'
                if (preg_match('/(\w+)\s*=\s*["\']([^"\']+)["\']/i', $where, $whereMatch)) {
                    $column = $whereMatch[1];
                    $value = $whereMatch[2];
                    $queryParams[] = $column . '=eq.' . urlencode($value);
                    $SUPABASE_DEBUG['where_parsed'] = $whereMatch;
                }
            }

            if ($limit) {
                $queryParams[] = 'limit=' . $limit;
            }

            if (!empty($queryParams)) {
                $endpoint .= '?' . implode('&', $queryParams);
            }

            $SUPABASE_DEBUG['endpoint'] = $endpoint;
            $this->result = $this->request('GET', $endpoint);

            if ($this->result !== false) {
                $this->num_rows = is_array($this->result) ? count($this->result) : 0;
                return true;
            }
        }

        $this->error = "No se pudo parsear el SELECT: " . $sql;
        return false;
    }

    private function executeInsert($sql)
    {
        // INSERT básico
        if (preg_match('/INSERT\s+INTO\s+(\w+)\s*\((.*?)\)\s*VALUES\s*\((.*?)\)/i', $sql, $matches)) {
            $table = $matches[1];
            $columns = array_map('trim', explode(',', $matches[2]));
            $values = array_map(function ($v) {
                return trim($v, " '\"");
            }, explode(',', $matches[3]));

            $data = array_combine($columns, $values);

            $this->result = $this->request('POST', '/' . $table, $data);

            if ($this->result !== false) {
                $this->affected_rows = 1;
                if (isset($this->result[0]['id'])) {
                    $this->insert_id = $this->result[0]['id'];
                }
                return true;
            }
        }

        $this->error = "No se pudo parsear el INSERT";
        return false;
    }

    private function executeUpdate($sql)
    {
        $this->error = "UPDATE aún no implementado en REST adapter";
        return false;
    }

    private function executeDelete($sql)
    {
        $this->error = "DELETE aún no implementado en REST adapter";
        return false;
    }

    public function get_result()
    {
        if ($this->result !== null && is_array($this->result)) {
            return new SupabaseRestResult($this->result);
        }
        return false;
    }

    public function store_result()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    private function request($method, $endpoint, $data = null)
    {
        $url = $this->baseUrl . $endpoint;

        // DEBUG: guardar la URL para inspección
        global $SUPABASE_LAST_URL;
        $SUPABASE_LAST_URL = $url;

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $this->error = curl_error($ch);
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($httpCode >= 400) {
            $this->error = "HTTP Error $httpCode: " . $response;
            return false;
        }

        return json_decode($response, true);
    }
}

class SupabaseRestResult
{
    private $data;
    private $position = 0;
    public $num_rows;

    public function __construct($data)
    {
        $this->data = is_array($data) ? $data : [];
        $this->num_rows = count($this->data);
    }

    public function fetch_assoc()
    {
        if ($this->position < count($this->data)) {
            return $this->data[$this->position++];
        }
        return null;
    }

    public function fetch_array()
    {
        return $this->fetch_assoc();
    }

    public function fetch_row()
    {
        $assoc = $this->fetch_assoc();
        return $assoc ? array_values($assoc) : null;
    }

    public function free()
    {
        $this->data = [];
    }
}
?>