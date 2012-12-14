<?
    class action extends fm_page {
        public $template = 'action';
        public $page_id = 0;
        public $page_table = 'event';

        public $upload_img;
        public $upload_photo;

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);
            func::error(false);

            $this->page_id = intval($this->f->pathURIAt(0));
            $description = func::post('description');
            $schedule = func::post('schedule');
            $date = func::dt2us(func::post('date'));

            $place = func::post('place');
            $locale = func::post('locale');

            $text = func::post('text');
            $active = func::postCheck('active');

            $this->upload_img = new image('image');
            $this->upload_photo = new image('photo');

            ## TEST ##
            func::error_empty($description, 'description', 'evento');
            func::error_empty($schedule, 'schedule', 'datas');
            func::error_empty($date, 'date', 'data de início');

            // image
            if ((!$this->page_id && $this->upload_img->error) || $this->upload_img->error == '2') {
                func::error('image', '<strong>Logotipo:</strong> '.$this->upload_img->errorMsg);
            }
            if ($this->upload_photo->error == '2') {
                func::error('image', '<strong>Foto:</strong> '.$this->upload_img->errorMsg);
            }

            ## SAVE ##
            if (!func::error()) {
                $uri = manager::getUnique($description, $this->page_table, 'id != '.$this->page_id);
                $fields = compact('uri', 'description', 'schedule', 'date', 'place', 'locale', 'text', 'active');
                manager::save($this, $this->page_table, $fields, $this->page_id, true);
            } else {
                $this->f->requirePage('edit/'.$this->page_id, -1);
            }
        }

        ## SAVE AFTER ##
        public function saveAfter(&$msg0, &$msg1) {
            $msg0 .= manager::image($this->page_table, $this->page_id, false, $this->upload_img, array(
                array('img0', '250', '250', 'ext'),
                array('img1', '500', '500', 'ext')
            ));

            $msg0 .= manager::image($this->page_table, $this->page_id, func::postCheck('photo_delete'), $this->upload_photo, array(
                array('photo', '580', '0', 'max')
            ));
        }
    }
?>