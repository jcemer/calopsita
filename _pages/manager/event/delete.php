<?
    class action extends fm_page{
        public $template = 'action';
        public $page_id = 0;
        public $page_table = 'event';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $this->page_id = intval($this->f->pathURIAt(0));

            manager::image($this->page_table, $this->page_id, true, false, array(
                array('img0'),
                array('img1')
            ));
            manager::image($this->page_table, $this->page_id, true, false, array(
                array('photo')
            ));

            if ($this->page_id) {
                manager::delete($this, 'DELETE FROM '.$this->page_table.' WHERE id = '.$this->page_id);
            } else {
                $this->f->redirect('index', -1);
            }
        }
    }
?>