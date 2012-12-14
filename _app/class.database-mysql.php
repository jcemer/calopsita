<?
    class DB {
        // only to mysql
        private static $ins;
        private $conn = '';

        private $host = '';
        private $pass = '';
        private $user = '';

        public function __construct($host = 'localhost', $user = '', $pass = '', $db = false) {
            $this->conn = false;
            $this->host = $host;
            $this->user = $user;
            $this->pass = $pass;

            $conn = mysql_connect($this->host, $this->user, $this->pass);
            if ($conn) {
                $this->conn = $conn;
                if ($db) {
                    mysql_select_db($db, $this->conn);
                }
            }
            self::$ins = $this;
            echo mysql_error();
        }

        public function __destruct() {
            if (is_resource($this->conn)) {
                mysql_close($this->conn);
            }
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
            return mysql_error($conn);
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
            return mysql_insert_id(DB::conn());
        }

        public static function affected() {
            return mysql_affected_rows(DB::conn());
        }

        private function fields_check($table, $fields){
            $rs = DB::exec('SHOW COLUMNS FROM '.$table);
            $values = array();
            while ($rs->row()) {
                $f = $rs->field('Field');
                if (array_key_exists($f, $fields)) {
                    $val = $fields[$f];
                    switch ($rs->fType('Type')) {
                        case 'C': $val = '"'.DB::str($val).'"'; break;
                        case 'D': $val = '"'.($val?$val:'0000-00-00').'"'; break;
                        case 'T': $val = '"'.($val?$val:'0000-00-00 00:00:00').'"'; break;
                        case 'F': $val = floatval($val); break;
                        case 'I': $val = intval($val); break;
                    }
                    $values[$f] = $val;
                }
            }
            return $values;
        }

        public static function str($v) {
            return mysql_real_escape_string(stripslashes($v), DB::conn());
        }

        // CRUD

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
        private $i = -1;

        private $res;

        public function __construct($sql = false, $conn = false) {
            if ($sql) {
                $this->qid = mysql_query(($this->sql = $sql), $conn);
                $this->num = @mysql_num_rows($this->qid);
            }
        }

        public function row() {
            $this->i++;
            if(!$this->qid)
                return false;
            return ($this->res = mysql_fetch_array($this->qid));
        }

        function field ($name, $fetch = false) {
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

        public function to($num) {
            if(!@ mysql_data_seek($this->qid, $num)) {
                return false;
            }
            $this->i = $num - 1;
            return ;
        }
        public function toFirst() {
            return $this->to(0);
        }
        public function toPrev($offset = 1) {
            return $this->to(($this->i + 1) - $offset);
        }
        public function toNext($offset = 1) {
            return $this->to(($this->i + 1) + $offset);
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