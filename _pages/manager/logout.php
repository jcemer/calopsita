<?
        class action extends fm_page{
                public $template = 'action';

                public function initialize(){
                        require_once(PATH_APP.'manager/bootstrap.php');
                        $GLOBALS['MANAGER']->logout();
                        $this->f->redirect('index', 1, 'msg='.func::encode('Acesso encerrado!'));
                }
        }
?>