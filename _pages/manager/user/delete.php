<?
    class action extends fm_page {
        public $template = 'action';

        public function initialize(){
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $id = intval($this->f->pathURIAt(0));
            if ($id) {
                manager::delete($this, 'DELETE FROM user WHERE id = '.$id);
            } else {
                $this->f->redirect('index');
            }
        }
    }
?>