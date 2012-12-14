<?
    class framework {
        private $pathPage;
        private $pathURI;
        public $page;

        public function __construct(){
            $_GET = func::addSlashes($_GET);
            $_POST = func::addSlashes($_POST);
            $_COOKIE = func::addSlashes($_COOKIE);
            setlocale(LC_CTYPE, 'pt_BR');
            date_default_timezone_set('America/Sao_Paulo');

            if (DB_CONN) {
                new DB(DB_DRIVER, DB_HOST, DB_USER, DB_PWD, DB_DATA);
            }

            ini_set('display_errors', ERROR_DEBUG);
            ini_set('log_errors', 'On');
            ini_set('error_log', PATH_FILE_ERROR);
            ini_set('error_reporting', E_ALL);

            $match = array();
            $regexp = '/^\/'.str_replace('/', '\/', PATH_INVALID).'([^?]*)/';

            if (URL_REWRITE) {
                if (!preg_match($regexp, $_SERVER['REQUEST_URI'], $match)) {
                    $this->redirect();
                }
                $match = $match[1];
            } else {
                $match = func::getCheck('p');
            }

            if (preg_match('/\./', $match)) {
                $this->getPage404();
            } else {
                $this->checkPathURI($match);
                $this->getPage();
            }
        }

        // PRIVATE

        private function checkPathURI($path) {
            $this->pathPage = array();
            $this->pathURI = explode('/', trim($path, '/'));
        }

        private function getPagePath($current = '') {
            return PATH_PAGES.implode('/', $this->pathPage).'/'.$current;
        }

        private function getPage() {
            do {
                $current = array_shift($this->pathURI);
                if (empty($current)) {
                    $current = 'index';
                } else if (in_array($current, scandir($this->getPagePath()))) {
                    $this->pathPage[] = $current;
                    $current = false;
                }
            } while(!$current);
            $path = $this->getPagePath($current).'.php';

            if (file_exists($path)) {
                include_once($path);
                if ($current != 'index') {
                    $this->pathPage[] = $current;
                }
                $GLOBALS['PATH_PAGE'] = $this->pathPage;

                // page
                if (class_exists('page', false)) {
                    $this->page = new page($this);
                    $this->page->show();
                // action
                } else if (class_exists('action', false)) {
                    new action($this);
                }
            } else {
                $this->getPage404();
            }
        }

        private function getPage404() {
            @include(PATH_PAGES.'_404.php');
            if (class_exists('page', false)) {
                $this->page = new page($this);
                $this->page->show();
            }
        }

        // PUBLIC

        public function redirect($path = '', $deep = 0, $qs = '') {
            header('Location: /'.PATH_INVALID.func::link($path, $deep, $qs));
            exit;
        }

        public function requirePage($path = '', $deep = 0) {
            $this->checkPathURI(func::link($path, $deep, '', true));
            $this->getPage();
        }

        public function pathURI() {
            return $this->pathURI;
        }

        public function pathURIAt($num) {
            return !empty($this->pathURI[$num]) ? $this->pathURI[$num] : false;
        }

        public function getPageName() {
            if (!empty($this->page->name)) {
                return $this->page->name;
            }
            return end($this->pathURI);
        }
    }
?>