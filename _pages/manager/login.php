<?
    class action extends fm_page {
        public $template = 'action';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');

            $login = func::postCheck('login');
            $password = func::postCheck('password');

            if (empty($login) || empty($password)) {
                $this->f->redirect('index', 1, 'msg='.func::encode('Informe login e senha.'));
            } else {
                if ($row = DB::row('SELECT id, description FROM user WHERE active = 1 AND login = "'.$login.'" AND password = "'.md5($password).'"')) {
                    $GLOBALS['MANAGER']->login($row['id'], $row['description']);
                    $this->f->redirect('entrance');
                } else {
                    $this->f->redirect('index', 1, 'msg='.func::encode('Login ou senha incorretos.'));
                }
            }
        }
    }
?>