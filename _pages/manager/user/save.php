<?
    class action extends fm_page {
        public $template = 'action';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);
            func::error(false);

            $id = intval($this->f->pathURIAt(0));
            $login = func::post('login');
            $active = func::postCheck('active');

            // description
            if (empty($id)) {
                $description = func::postCheck('description');
                if (
                    !func::error_empty($description, 'description', 'nome') &&
                    DB::row('SELECT id FROM user WHERE description = "'.$description.'"')
                ) {
                    func::error('description', 'Já existe um usuário com este <strong>nome</strong>');
                }
            }

            // login
            if (
                !func::error_empty($login, 'login', 'login') &&
                DB::row('SELECT id FROM user WHERE id != '.$id.' AND login = "'.$login.'"')
            ) {
                func::error('login', 'Já existe um usuário com este <strong>login</strong>');
            } else if (!func::checkUnixName($login)) {
                func::error('login', 'O <strong>login</strong> não pode conter caracteres especiais ou espaços');
            }

            // passwords
            if (empty($id) || func::postCheck('password_change')) {
                $password = func::post('password');
                $password_confirm = func::post('password_confirm');
                if (empty($password) || empty($password_confirm)) {
                    func::error('password', 'As <strong>senhas</strong> devem ser preenchidas');
                } else if (strlen($password) < 4) {
                    func::error('password', 'A <strong>senha</strong> deve conter quatro ou mais caracteres');
                } else if ($password !== $password_confirm) {
                    func::error('password', 'As <strong>senhas</strong> não conferem');
                }
            }

            ## SAVE ##
            if (!func::error()) {
                $fields = compact('login', 'active');
                if (empty($id)) {
                    $fields['description'] = $description;
                    $fields['password'] = md5($password);
                } else if (func::postCheck('password_change')) {
                    $fields['password'] = md5($password);
                }
                manager::save($this, 'user', $fields, $id);
            } else {
                $this->f->requirePage('edit/'.$id);
            }
        }
    }
?>