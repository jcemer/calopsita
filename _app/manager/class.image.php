<?
    class image {
        private $input;
        private $type;
        private $path;
        private $time;

        public $error = false;
        public $errorMsg;

        private $oldImg;

        public function image($input, $path = false, $num = -1) {
            if ($num == -1) {
                $this->input = $_FILES[$input];
            } else {
                foreach($_FILES[$input] as $key => $value) {
                    $this->input[$key] = $value[$num];
                }
            }
            $this->path = $path;
            $this->time = time();

            // checando upload e GD
            if (empty($this->input['name'])){
                    $this->error = 1;
                    $this->errorMsg = "Imagem não enviada ao servidor.\n";
            } else {
                if ($this->input['error'] > 0 || !is_uploaded_file($this->input['tmp_name'])) {
                    $this->error = 2;
                    $this->errorMsg = "Imagem não enviada ao servidor, erro no envio.";
                } else if (!function_exists('gd_info')) {
                    $this->error = 2;
                    $this->errorMsg = "Bliblioteca GD não instalada, operação cancelada.";
                }
            }

            // criando imagem
            if (!$this->error) {
                switch($this->input['type']) {
                    case 'image/pjpeg':
                    case 'image/jpeg':
                        $this->type = 'JPG';
                        $this->oldImg = imagecreatefromjpeg($this->input['tmp_name']); break;

                    case 'image/gif':
                        $this->type = 'GIF';
                        $this->oldImg = imagecreatefromgif($this->input['tmp_name']); break;

                    case 'image/png':
                        $this->type = 'PNG';
                        $this->oldImg = imagecreatefrompng($this->input['tmp_name']); break;

                    default:
                        $this->error = 2;
                        $this->errorMsg = "Formato de arquivo inválido.\n"; break;
                }
            }

            // testando criacao de arq
            if (!$this->error && !$this->oldImg) {
                $this->error = 2;
                $this->errorMsg = "Impossível criar imagem, arquivo com falhas.\n";
            }
        }

        private function getScala($new, $old) {
            if (empty($new) || $old < $new) {
                return 1;
            } else {
                return $new / $old;
            }
        }

        private function getSize($newImg_x, $newImg_y, $oldImg_x, $oldImg_y, $cond) {
            $scala_x = $this->getScala($newImg_x, $oldImg_x);
            $scala_y = $this->getScala($newImg_y, $oldImg_y);

            if ($cond == 'max') { // !logica invertida!
                $scala = min($scala_x, $scala_y);
            } else { // serve para max e ext
                $scala = max($scala_x, $scala_y);
            }

            $size['x'] = round($oldImg_x * $scala);
            $size['y'] = round($oldImg_y * $scala);

            return $size;
        }

        public function save($imgs, $path = false, $id = false) {
            if ($path) {
                $this->path = $path;
            }
            $oldImg_x = imagesx($this->oldImg);
            $oldImg_y = imagesy($this->oldImg);

            $fields = array();
            if (!empty($imgs)) {
                foreach ($imgs as $img) {
                    $size = $this->getSize($img[1], $img[2], $oldImg_x, $oldImg_y, $img[3]);

                    if ($img[3] == 'ext') {
                        $createX = min($size['x'], $img[1]);
                        $createY = min($size['y'], $img[2]);
                        $dstX = ($size['x'] - $createX)/-2;
                        $dstY = ($size['y'] - $createY)/-2;
                    } else {
                        $createX = $size['x'];
                        $createY = $size['y'];
                        $dstX = $dstY = 0;
                    }
                    // create
                    if ($this->type == 'GIF') {
                        $newImg = imagecreate($createX, $createY);
                    } else {
                        $newImg = imagecreatetruecolor($createX, $createY);
                    }
                    imagecopyresampled($newImg, $this->oldImg, $dstX, $dstY, 0, 0, $size['x'], $size['y'], $oldImg_x, $oldImg_y);

                    if ($id) {
                        $img[4] = sprintf('%05d', $id).'-'.$img[0].'-'.$this->time.'.'.strtolower($this->type);
                    }

                    // save
                    switch($this->type){
                        case 'JPG': imagejpeg($newImg, $this->path.$img[4], 90); break;
                        case 'GIF': imagegif($newImg, $this->path.$img[4]); break;
                        case 'PNG': imagepng($newImg, $this->path.$img[4]); break;
                    }
                    chmod($this->path.$img[4], 0777);

                    $fields[$img[0].'_type'] = $this->type;
                    $fields[$img[0].'_width'] = $createX;
                    $fields[$img[0].'_height'] = $createY;
                    $fields[$img[0].'_file'] = $img[4];
                    imagedestroy($newImg);
                }
            }

            imagedestroy($this->oldImg);
            if ($id) {
                $fields['id'] = $id;
            }
            return $fields;
        }
    }
?>