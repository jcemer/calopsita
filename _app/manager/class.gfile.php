<?
    class gfile {
        private $input;
        private $path;

        public $size;

        public $error = false;
        public $errorMsg;

        public function gfile($input, $path = false) {
            $this->input = $_FILES[$input];
            $this->path = $path;

            if (empty($this->input['name'])) {
                $this->error = 1;
                $this->errorMsg = "Arquivo não enviado ao servidor.";
            } else if($this->input['error']>0 || !is_uploaded_file($this->input['tmp_name'])) {
                $this->error = 2;
                $this->errorMsg = "Arquivo não enviado ao servidor, erro no envio.";
            } else {
                $this->size = $this->input['size'];
            }
        }

        public function save($name, $path) {
            if ($path) {
                $this->path = $path;
            }
            move_uploaded_file($this->input['tmp_name'], $this->path.$name);
            chmod($this->path.$name, 0777);
            return $name;
        }

        public function delete($array) {
            if(!empty($array)) {
                foreach($array as $arq) {
                    @unlink($this->path.$arq);
                }
            }
        }

        public function getName() {
            return $this->input['name'];
        }
    }
?>