<?
    class func {

        public static function execute($var, $func) {
            if (is_array($var)) {
                foreach ($var as $index => $value) {
                    $var[$index] = func::execute($value, $func);
                }
            } else {
                $var = $func($var);
            }
            return $var;
        }

        public static function addSlashes($var) {
            if(!get_magic_quotes_gpc())
                $var = func::execute($var, 'addslashes');
            return $var;
        }

        public static function cleanStr($var, $htmlspecialchars) {
            if ($htmlspecialchars) {
                $var = func::execute($var, 'htmlspecialchars');
            }
            return func::execute($var, 'trim');
        }

        public static function encode($var) {
            return urlencode($var);
        }

        public static function implode($glue, $pieces, $tail = false){
            if ($tail && !empty($pieces)) {
                return $glue.implode($tail.$glue, $pieces).$tail;
            }
            return implode($glue, $pieces);
        }

        public static function nls2p($var) {
            $var = preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p><p>', $var);
            return str_replace('<p></p>', '', '<p>'.nl2br($var).'</p>');
        }

        public static function strtr_utf8($str, $from, $to) {
            $keys = array();
            $values = array();
            preg_match_all('/./u', $from, $keys);
            preg_match_all('/./u', $to, $values);
            $mapping = array_combine($keys[0], $values[0]);
            return strtr($str, $mapping);
        }

        public static function unixName($var, $maxlength = 40){
            $var = str_replace("ß", "ss", $var);
            $var = mb_ereg_replace('/[ÄÆÖØÜäæöøü]/', '\0e', $var);

            $var = func::strtr_utf8($var, '¢¥²³¹ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖ×ØÙÚÛÜÝàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿ',
                                          'cY231AAAAAAACEEEEIIIINOOOOOxOUUUUYaaaaaaaceeeeiiiinoooooouuuuyy');
            $var = mb_ereg_replace('/[^a-z0-9-]+/i', '_', $var);
            $var = mb_ereg_replace('/\b_*\b/', '', $var); // ex "_a_a_._a_" => "a_a.a"
            $var = str_replace('_', '-', $var);

            while (strlen($var) > $maxlength) {
                $var = mb_ereg_replace('/.\b/', '', $var, 1);
            }
            return strtolower($var);
        }

        // POST
        public static function post($var, $htmlspecialchars = false) {
            if (!isset($_POST[$var])) {
                echo 'Erro ao receber '.$var; exit;
            }
            return func::cleanStr($_POST[$var], $htmlspecialchars);
        }

        public static function postCheck($var, $default = false, $htmlspecialchars = false) {
            if (!isset($_POST[$var])) {
                return $default === false ? 0 : $default;
            } else {
                return func::post($var, $htmlspecialchars);
            }
        }

        // GET
        public static function get($var, $htmlspecialchars = false) {
            if (!isset($_GET[$var])) {
                echo 'Erro ao receber '.$var; exit;
            }
            return func::cleanStr($_GET[$var], $htmlspecialchars);
        }
        public static function getCheck($var, $default = false, $htmlspecialchars = false) {
            if (!isset($_GET[$var])) {
                return $default === false ? 0 : $default;
            } else {
                return func::get($var, $htmlspecialchars);
            }
        }

        // JSON
        public static function json($data) {
            if (func::getCheck('callback')) {
                return func::getCheck('callback').'('.json_encode($data).')';
            } else {
                header('Access-Control-Allow-Origin: *');
                return json_encode($data);
            }
        }
        public static function jsonp($data) {
            return func::getCheck('callback').'('.json_encode($data).')';
        }

        // ROW
        public static function row(&$row, $var) {
            return htmlspecialchars($row[$var]);
        }
        public static function rowFormat(&$row, $var) {
            $ret = nl2br(func::row($row, $var));
            $ret = str_replace("\n", '', $ret);
            $ret = str_replace("\r", '', $ret);
            return $ret;
        }

        // POST OR ROW
        public static function postOrRow(&$row, $var, $default = '') {
            $ret = $default;
            if (!empty($_POST)) {
                $ret = stripslashes(func::postCheck($var, $default, true));
            } else if (isset($row[$var])) {
                $ret = func::row($row, $var);
            }
            return $ret;
        }
        public static function postOrRowCheck(&$row, $var, $value, $default = '') {
            if (func::postOrRow($row, $var, $default) == $value) {
                return ' checked="checked" ';
            }
        }
        public static function postOrRowSelect(&$row, $var, $value, $default = ''){
            if (func::postOrRow($row, $var, $default) == $value) {
                return ' selected="selected" ';
            }
        }

        // MSG
        public static function msg($id, $msg = '') {
            $ret = '<div id="'.$id.'">';
            if (is_array($msg)) {
                $msg = array_filter($msg);
                if (!empty($msg)) {
                    $ret .= '<ul>'.func::implode('<li>', $msg, '</li>').'</ul>';
                }
            }
            $ret .= ' </div>'."\n";
            return $ret;
        }
        public static function msgList() {
            $ret = '';
            if (!empty($_GET['msg1'])) {
                $ret .= func::msg('msg1', explode("\n", $_GET['msg1']));
            }
            if (!empty($_GET['msg0'])) {
                $ret .= func::msg('msg0', explode("\n", $_GET['msg0']));
            }
            return $ret;
        }

        // CHECKS
        public static function checkEmail($mail) {
            // same as class email_message
            return preg_match("/^[\-\!\#\$\%\&\'\*\+\.\/0-9\=\?A-Z\^\_\`a-z\{\|\}\~]+\@([\-\!\#\$\%\&\'\*\+\/0-9\=\?A-Z\^\_\`a-z\{\|\}\~]+\.)+[a-zA-Z]{2,6}$/", $mail);
        }
        public static function checkUnixName($var) {
            return !!preg_match('/^[0-9a-zA-Z\.\-\_]+$/', $var);
        }

        // LINK
        public static function link($uri, $deep = null, $qs = '', $url_rewrite = false){
            $uri = trim($uri, '/');
            $path = $GLOBALS['PATH_PAGE'];

            if ($deep === null) {
                $path = $GLOBALS['PATH_PAGE'];
            } else {
                $path = array_slice($GLOBALS['PATH_PAGE'], 0, $deep);
            }
            if (!empty($path)) {
                $uri = implode('/', $path).'/'.$uri;
            }

            if (!URL_REWRITE && !$url_rewrite) {
                return '?p='.strtolower($uri).($qs ? '&amp;'.$qs : '');
            } else {
                return strtolower($uri).($qs ? '?'.$qs : '');
            }
        }

        // TRUNCATE
        public static function truncate($str, $len) {
            if (strlen($str) < $len) {
                return $str;
            } else {
                $str = substr($str, 0, $len);
                while(substr($str, -1) != ' ' && strlen($str)) {
                    $str = substr($str, 0, strlen($str)-1);
                }
                return substr($str, 0, strlen($str)-1).'...';
            }
        }

        // DATES
        public static function checkDtbr($dia, $mes, $ano) {
            if (checkdate($mes, $dia, $ano)) {
                return $dia.'.'.$mes.'.'.$ano;
            }
        }

        public static function dt2br($var) {
            if (preg_match('/([012][0-9]|3[01]|[0-9])\/([0-9]|[0-9]{2})\/([0-9]{4})/', $var, $array)) {
                return func::checkDtbr($array[1], $array[2], $array[3]);
            } else if(preg_match('/([0-9]{4})\-([0-9]|[0-9]{2})\-([012][0-9]|3[01]|[0-9])/', $var, $array)) {
                return func::checkDtbr($array[3], $array[2], $array[1]);
            }
        }
        public static function dttm2br($var) {
            if (preg_match('/([0-9]{4})\-([0-9]{2})\-([012][0-9]|3[01])[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/', $var, $array)) {
                $dt = func::checkDtbr($array[3], $array[2], $array[1]);
                if ($dt && ($array[4] != '00' || $array[5] != '00')) {
                    $dt .= '  '.$array[4].':'.$array[5];
                }
                return $dt;
            }
        }

        public static function checkDtus($dia, $mes, $ano){
            if (checkdate($mes, $dia, $ano)) {
                return $ano.'-'.$mes.'-'.$dia;
            }
        }
        public static function dt2us($var) {
            if (preg_match('/([0-9]{4})\-([0-9]|[0-9]{2})\-([012][0-9]|3[01]|[0-9])/', $var, $array)) {
                return func::checkDtus($array[3], $array[2], $array[1]);
            } else if(preg_match('/([012][0-9]|3[01]|[0-9])[\/.]([0-9]|[0-9]{2})[\/.]([0-9]{4})/', $var, $array)) {
                return func::checkDtus($array[1], $array[2], $array[3]);
            }
        }

        // SIZE
        function size($size, $round = 0) {
            $label = array('B', 'KB', 'MB', 'GB');
            for ($i = 0; $size > 1024 && $i < count($label) - 1; $i++) {
                $size /= 1024;
            }
            return round($size, $round).$label[$i];
        }

        // ERROR
        public static function error($id = true, $msg = '') {
            if (!$id) {
                $GLOBALS['editMsg'] = '';
            } else if ($id === true) {
                return !empty($GLOBALS['editMsg']);
            } else {
                $GLOBALS['editMsg'] .= str_replace("\n", '', $msg).' <a href="#" class="iGo" title="'.$id.'" onclick="return fields.go(\'#'.$id.'\')">| verificar</a>'."\n";
            }
        }
        public static function error_true($bool, $id, $msg) {
            if ($bool) {
                func::error($id, $msg);
            }
        }
        public static function error_empty($val, $id, $name) {
            if (empty($val)) {
                func::error($id, 'O campo <strong>'.$name.'</strong> deve ser preenchido corretamente');
                return false;
            }
        }
    }
?>