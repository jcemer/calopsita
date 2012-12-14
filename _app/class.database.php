<?
    class DB {
        private static $ins;
        private $conn = '';

        public function __construct($driver = 'mysql', $host = 'localhost', $user = '', $password = '', $dbname = false) {
            $this->conn = false;

            $dsn = $driver.':host='.$host.';';
            if ($dbname) {
                $dsn .= 'dbname='.$dbname.';';
            }
            try {
                $conn = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
                $this->conn = $conn;
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
            self::$ins = $this;
        }

        public static function conn() {
            $ins = DB::$ins;
            if ($ins) {
                return $ins->conn;
            }
            return false;
        }

        // -- STATIC --

        public static function exec($sql) {
            $conn = DB::conn();
            if (!$conn) {
                return new DB_recordset();
            }
            return new DB_recordset($sql, $conn);
        }

        public static function error() {
            $conn = DB::conn();
            if (!$conn) {
                return false;
            }
            $error = $conn->errorInfo();
            return intval($error[0]);
        }

        public static function row($sql) {
            $sql = preg_replace('/LIMIT [0-9]*$/', '', $sql);
            $rs = DB::exec($sql.' LIMIT 1');
            return $rs->row();
        }

        public static function field($sql) {
            $sql = preg_replace('/LIMIT [0-9]*$/', '', $sql);
            $rs = DB::exec($sql.' LIMIT 1');
            return $rs->field(0, true);
        }

        public static function toArray($sql) {
            $rs = DB::exec($sql);
            $ret = array();
            while ($row = $rs->row()) {
                $ret[$rs->field(0)] = $row;
            }
            return $ret;
        }

        public static function insert_id() {
            $conn = DB::conn();
            return $conn->lastInsertId();
        }

        private function fields_check($table, $fields, $type = true){
            $rs = DB::exec('SHOW COLUMNS FROM '.$table);
            $values = array();
            while ($rs->row()) {
                $f = $rs->field('Field');
                if (array_key_exists($f, $fields)) {
                    $val = $fields[$f];
                    if ($type) {
                        switch ($rs->fType('Type')) {
                            case 'C': $val = DB::str($val); break;
                            case 'D': $val = "'".($val?$val:'0000-00-00')."'"; break;
                            case 'T': $val = "'".($val?$val:'0000-00-00 00:00:00')."'"; break;
                            case 'F': $val = floatval($val); break;
                            case 'I': $val = intval($val); break;
                        }
                    }
                    $values[$f] = $val;
                }
            }
            return $values;
        }

        public static function str($var) {
            $conn = DB::conn();
            return $conn->quote(stripslashes($var));
        }

        // CRUD

        public static function select($table, $fields, $where = false) {
            $fields = DB::fields_check($table, $fields, false);
            if (!$fields) {
                return false;
            }
            if (!empty($fields['id']) && !$where) {
                $where = 'id = '.$fields['id'];
            }
            return DB::exec('SELECT '.implode(',', array_keys($fields)).' FROM '.$table.($where ? ' WHERE '.$where : ''));
        }

        public static function insert($table, $fields, $by = 'Website') {
            if ($by !== false) {
                $fields['created'] = date('Y-m-d H:i:s');
                $fields['created_by'] = $by;
            }
            $fields = DB::fields_check($table, $fields);

            if (!$fields) {
                return false;
            }
            $rs = DB::exec('INSERT INTO '.$table.' ('.implode(',', array_keys($fields)).') VALUES('.implode(',', $fields).')');
            if (!$rs->qid()) {
                return false;
            }
            $id = DB::insert_id();
            return $id ? $id : true;
        }

        public static function update($table, $fields, $where = false, $by = 'Website') {
            if ($by !== false) {
                $fields['modified'] = date('Y-m-d H:i:s');
                $fields['modified_by'] = $by;
            }
            $fields = DB::fields_check($table, $fields);

            if (!$fields) {
                return false;
            }
            if (!empty($fields['id']) && !$where) {
                $where = 'id = '.$fields['id'];
            }
            $values = array();
            foreach ($fields as $k => $v) {
                $values[] = $k.' = '.$v;
            }
            $rs = DB::exec('UPDATE '.$table.' SET '.implode(',', $values).($where ? ' WHERE '.$where : ''));
            return $rs->qid();
        }
    }



    class DB_recordset {
        private $sql = false;
        private $qid = false;

        private $num = false;
        private $prev = false;

        private $res;

        public function __construct($sql = false, $conn = false) {
            if ($sql) {
                $this->qid = $conn->query($this->sql = $sql);
                if ($this->qid) {
                    $this->num = $this->qid->rowCount();
                }
            }
        }

        public function row() {
            if ($this->prev) {
                $this->prev = false;
                return $this->res;
            }

            if (!$this->qid) {
                return false;
            }
            return ($this->res = $this->qid->fetch());
        }

        public function all() {
            if (!$this->qid) {
                return array();
            }
            return $this->qid->fetchAll(PDO::FETCH_ASSOC);
        }

        function field($name, $fetch = false) {
            if (
                (!$fetch && empty($this->res)) ||
                ($fetch && !$this->row()) ||
                !isset($this->res[$name])
            ) {
                return false;
            }
            return $this->res[$name];
        }
        function fUpper($name) {
            return strtoupper($this->field($name));
        }
        function fNL($name) {
            return str_replace(array("\n", "\r"), '', nl2br($this->field($name)));
        }
        function fType($name) {
            $t = strtoupper(preg_replace('/^([a-z]+)(\(.*)?$/', '$1', $this->field($name)));
            switch($t) {
                case 'TINYINT':
                case 'SMALLINT':
                case 'MEDIUMINT':
                case 'INT':
                case 'INTEGER':
                case 'BIGINT':
                    return 'I';

                case 'DECIMAL':
                case 'FLOAT':
                case 'DOUBLE':
                case 'REAL':
                    return 'F';

                case 'DATE':
                case 'YEAR':
                    return 'D';

                case 'DATETIME':
                case 'TIME':
                case 'TIMESTAMP':
                    return 'T';
            }
            return 'C';
        }

        public function toPrev() {
            if ($this->prev) {
                return false;
            }
            return $this->prev = true;
        }
        public function toNext() {
            $this->row();
            return true;
        }

        // GETTERS
        public function sql() {
            return $this->sql;
        }
        public function qid() {
            return $this->qid;
        }
        public function num() {
            return $this->num;
        }
        public function i() {
            return $this->i;
        }
    }
?>