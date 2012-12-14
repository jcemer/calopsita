<?
    class manager {
        private $qsStr;
        private $qsExt;
        private $order_fields;
        public $orderSql;

        private $control;

        public function __construct(){
            // usuario
            $this->control = USER_CONTROL;

            // querystring
            $this->qsExt = array('search' => '', 'page' => 1, 'msg' => '', 'msg0' => '', 'msg1' => '', 'order_field' => '', 'order_order' => '');
            foreach($_GET as $var => $value) {
                if (array_key_exists($var, $this->qsExt)) { // predifinidas
                    $this->setQsExt($var, stripslashes(func::get($var)));
                } else if (
                    !empty($value) &&
                    !in_array($var, array('x', 'y', 'goback', 'p')) &&
                    substr($var, 0, 2) != 'id'
                ) {
                    $this->qsStr .= '&'.$var.'='.func::encode(stripslashes($value));
                }
            }
        }


        public function getQsExt($var) {
            return htmlentities($this->qsExt[$var]);
        }
        public function getQsExtSQL($var) {
            return addslashes($this->qsExt[$var]);
        }
        public function setQsExt($var, $value) {
            $this->qsExt[$var] = $value;
        }


        private function qsStrExtra() {
            $queryString = $this->qsStr;
            $arrayExt = func_get_args();
            foreach($arrayExt as $value) {
                $queryString .= '&'.$value.'='.func::encode($this->qsExt[$value]);
            }
            return substr($queryString, 1);
        }
        public function qsStr($htmlentities = true) {
            $queryString = $this->qsStrExtra('search', 'page', 'order_field', 'order_order');
            if ($htmlentities) {
                $queryString = htmlentities($queryString);
            }
            return $queryString;
        }
        public function qsStrPagination() {
            return htmlentities($this->qsStrExtra('search', 'order_field', 'order_order'));
        }
        public function qsStrOrder() {
            return htmlentities($this->qsStrExtra('search'));
        }


        public function checkOrder($order_fields, $field_pattern, $order_pattern) {
            $this->order_fields = $order_fields;

            if (!array_key_exists($this->getQsExtSQL('order_field'), $this->order_fields)) {
                $this->setQsExt('order_field', $field_pattern);
            }
            if (!($this->getQsExtSQL('order_order') == 'ASC' || $this->getQsExtSQL('order_order') == 'DESC')) {
                $this->setQsExt('order_order', $order_pattern);
            }
            // sql order
            $this->orderSql = ' ORDER BY';
            if ($this->getQsExtSQL('order_field')) {
                $this->orderSql .= ' '.$this->getQsExtSQL('order_field').' '.$this->getQsExtSQL('order_order').',';
            }
            $this->orderSql .= ' id '.$this->getQsExtSQL('order_order');
        }

        public function linkOrder($num) {
            $field = array_slice($this->order_fields, $num, 1);
            if ($this->getQsExtSQL('order_field') == key($field)) {
                $order = $this->getQsExtSQL('order_order') == 'ASC' ? 'DESC' : 'ASC';
                $direction = ($this->getQsExtSQL('order_order') == 'ASC' ? 'down' : 'up').'OK';
            } else {
                $order = 'ASC';
                $direction = 'down';
            }
            $qs = $this->qsStrOrder().'&amp;order_field='.key($field).'&amp;order_order='.$order;
            return '<a href="'.func::link('', null, $qs).'" class="order '.$direction.'"><span>'.current($field).'</span></a>';
        }

        // USER

        public function login($id, $name) {
            $_SESSION['CONTROL'] = $this->control;
            $_SESSION['ID'] = $id;
            $_SESSION['NAME'] = $name;
            $this->userLog('E');
        }

        public function logout() {
            $this->userLog('X');
            $_SESSION['CONTROL'] = '';
            $_SESSION['ID'] = '';
            $_SESSION['NAME'] = '';
            session_destroy();
        }

        public function logged(&$self) {
            if (!empty($self->name_title)) {
                $self->setTitle('Gerenciador - '.$self->name_title);
            }
            if (empty($_SESSION['CONTROL']) || $_SESSION['CONTROL'] != $this->control) {
                $self->f->redirect('index', 1, 'msg=Efetue+login+para+ter+acesso+ao+sistema.');
            }
        }
        public function loggedIndex(&$self) {
            $self->setTitle('Gerenciador');
            if(!empty($_SESSION['CONTROL']) && $_SESSION['CONTROL'] == $this->control) {
                $self->f->redirect('entrance');
            }
        }

        public function userLog($action) {
            DB::exec('INSERT INTO user_log (description, date, action) VALUES ("'.$this->getName().'", NOW(), "'.$action.'")');
        }
        public function welcome(){
            $month = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
            return '<strong>Olá '.$this->getName().'!</strong> Hoje é '.date('d').' de '.$month[intval(date('m'))].' de '.date('Y');
        }


        public static function getID() {
            if (!empty($_SESSION['ID'])) {
                return $_SESSION['ID'];
            }
        }
        public static function getName(){
            if (!empty($_SESSION['NAME'])) {
                return $_SESSION['NAME'];
            }
        }


        /* ***********************
            STATIC
        *********************** */

        public static function getUnique($var, $table, $where = false, $field = 'uri', $sufix = '', $size = 190) {
            $sql = 'SELECT id FROM '.$table.' WHERE ';
            if ($where) {
                $sql .= $where.' AND ';
            }

            $var = $name = func::unixName($var, $size);
            for ($i = 2; DB::field($sql.$field.' = "'.$var.$sufix.'"'); $i++) {
                $var = $name.'-'.$i;
            }
            return $var.$sufix;
        }
        public static function getUniqueFileName($name, $table, $where = false, $field = 'file', $size = 190) {
            $dot = strrpos($name, ".");
            $extension = substr($name, $dot);
            $name = substr($name, 0, $dot);
            return manager::getUnique($name, $table, $where, $field, $extension, $size);
        }

        public static function menu($array, $select) {
            $ret = '<ul>'."\n";
            foreach ($array as $instance => $item) {
                if ($select == $instance) {
                    $ret .= '<li class="select"><a href="'.func::link($item[1], 1).'" title="'.$item[0].'">'.$item[0].'</a></li>'."\n";
                } else {
                    $ret .= '<li><a href="'.func::link($item[1], 1).'" title="'.$item[0].'">'.$item[0].'</a></li>'."\n";
                }
            }
            return $ret.'</ul>'."\n";
        }

        public static function panel($var = '') {
            echo '<div id="panel">'."\n";
            echo '  <div class="icons">'.$var.'</div>'."\n";
            echo '  <form action="" class="search">'."\n";
            echo '    <fieldset>'."\n";
            echo '      <input name="p" type="hidden" value="'.func::getCheck('p').'" />'."\n";
            echo '      <input name="search" type="text" class="field" title="Buscar por" value="'.$GLOBALS['MANAGER']->getQsExt('search').'" />'."\n";
            echo '      <button type="submit" class="btn" title="buscar">buscar</button>'."\n";
            echo '    </fieldset>'."\n";
            echo '  </form>'."\n";
            echo '</div>'."\n";
            echo func::msgList();
        }

        public static function roll(&$self, $sql) {
            $qs = $GLOBALS['MANAGER']->getQsExtSQL('page');
            $pag = new pagination($sql, $qs);
            if ($pag->rs->num()) {
                echo '<table class="roll">'."\n";
                $self->roll($pag->rs);
                echo '</table>'."\n";
            } else {
                echo '<p class="error">Nenhum registro encontrado.</p>'."\n";
            }
        }

        public static function registre(&$self, $form = true, $formButton = false) {
            if (!isset($self->page_id) || !$self->page_id || $self->page_row) {
                if (isset($self->page_id)) {
                    $id = $self->page_id;
                } else {
                    $id = '';
                }

                echo '<div id="registre">'."\n";

                $msg = 'editMsg';
                echo func::msg($msg, !empty($GLOBALS[$msg]) ? explode("\n", $GLOBALS[$msg]) : '');

                if ($form) {
                    echo '<form action="'.func::link('save/'.$id, 2, $GLOBALS['MANAGER']->qsStr()).'"  method="post" enctype="multipart/form-data">'."\n";
                }
                echo $self->registre();
                if ($form) {
                    echo '<p class="footer"><button type="submit" class="btn">'.($formButton ? $formButton : ($id ? 'alterar' : 'inserir')).'</button></p>'."\n";
                    echo '</form>'."\n";
                }
                echo '</div>'."\n";
                echo '<div id="wait" style="display:none"><img src="images/manager/loading.gif" width="32" height="32" alt="loading" /></div>'."\n";
            } else {
                echo '<p class="error">Registro não encontrado.</p>'."\n";
            }
        }
        public static function registreInfo(&$row, $line = true) {
            if ($row['created']) {
                $ret = '<p><strong>Criado em</strong> '.func::dttm2br($row['created']).'<br />&nbsp;<strong>por</strong> '.func::row($row, 'created_by').'</p>'."\n";
                if($row['modified']) {
                    $ret .= '<p><strong>Modificado em</strong> '.func::dttm2br($row['modified']).'<br />&nbsp;<strong>por</strong> '.func::row($row, 'modified_by').'</p>'."\n";
                }
                if ($line) {
                    $ret .= '<hr />'."\n";
                }
                return $ret."\n";
            }
        }

        public static function image($table, $id, $delete, $upload, $files) {
            $path = PATH_UPLOAD.$table.'/';
            $upload_error = $upload ? $upload->error : 10;
            if (!$upload_error) {
                $delete = true;
            }

            // delete
            if ($delete) {
                $fields = array('id' => $id);
                foreach ($files as $file) {
                    $fields[$file[0].'_width'] = '';
                    $fields[$file[0].'_height'] = '';
                    $fields[$file[0].'_file'] = '';
                }
                $row = DB::select($table, $fields)->row();
                if ($row) {
                    foreach ($files as $file) {
                        @unlink($path.$row[$file[0].'_file']);
                    }
                }
                DB::update($table, $fields, false, false);
            }
            // save
            if (!$upload_error) {
                $fields = $upload->save($files, $path, $id);
                DB::update($table, $fields, false, false);
            } else if ($upload_error == '2') {
                return '<strong>Imagem:</strong> '.$upload->errorMsg;
            }
        }

        public static function gfile($table, $id, $delete, $upload = false, $file = 'file') {
            $path = PATH_UPLOAD.$table.'/';
            $upload_error = $upload ? $upload->error : 10;
            if (!$upload_error) {
                $delete = true;
            }

            // delete
            if ($delete) {
                $fields = array('id' => $id);
                $fields[$file] = '';
                $row = DB::select($table, $fields)->row();
                if ($row) {
                    @unlink($path.$row[$file]);
                }
            }
            // save
            if (!$upload_error) {
                $name = manager::getUniqueFileName($upload->getName(), $table, 'id != '.$id, $file);
                $fields = array('id' => $id);
                $fields[$file] = $upload->save($name, $path);
                $fields[$file.'_size'] = $upload->size;
                DB::update($table, $fields, false, false);
            } else if ($upload_error == '2') {
                return '<strong>Arquivo:</strong> '.$upload->errorMsg;
            }
        }

        // CRUD

        public static function save(&$self, $table, $fields, $id, $saveAfter = false, $redirect = true) {
            if ($id) {
                DB::update($table, $fields, 'id = '.$id, $GLOBALS['MANAGER']->getName());
            } else {
                $self->page_id = DB::insert($table, $fields, $GLOBALS['MANAGER']->getName());
            }

            if (!$redirect) {
                return DB::error();
            }

            // redirect
            if (DB::error() == 0) {
                $msg0 = '';
                $msg1 = '';
                if ($saveAfter) {
                    $self->saveAfter(&$msg0, &$msg1);
                }

                if (!$id) {
                    $msg = func::encode('Registro inserido com sucesso.');
                } else {
                    $msg = func::encode('Registro atualizado com sucesso.');
                }
                $self->f->redirect('index', -1, 'msg0='.$msg0.'&msg1='.$msg.$msg1.'&'.$GLOBALS['MANAGER']->qsStr(false));
            } else {
                $msg = func::encode('Erro ao manipular registro.');
                $self->f->redirect('index', -1, 'msg0='.$msg);
            }
        }

        public static function delete(&$self, $sql, $deleteAfter = false, $redirect = true) {
            $num = DB::exec($sql)->num();

            if (!$redirect) {
                return $num;
            }

            if ($num) {
                $msg0 = '';
                $msg1 = '';
                if ($deleteAfter) {
                    $msgs = $self->deleteAfter(&$msg0, &$msg1);
                }
                $msg = func::encode('Registro excluído com sucesso.');
                $self->f->redirect('index', -1, 'msg0='.$msg0.'&msg1='.$msg.$msg1.'&'.$GLOBALS['MANAGER']->qsStr(false));
            } else {
                $msg = func::encode('Erro ao excluir registro.');
                $self->f->redirect('index', -1, 'msg0='.$msg);
            }
        }
    }
?>