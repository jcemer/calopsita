<?
    abstract class fm_page {
        private $title;
        protected $info;
        public $template = 'default';
        public $f;

        final function __construct(&$fRef) {
            $this->f = &$fRef;
            $this->setTitle(PAGE_TITLE);
            $this->initialize();
        }

        protected function initialize() {
            if (!empty($this->subtitle)) {
                $this->addToTitle(' - '.$this->subtitle);
            }
        }

        final public function show() {
            $f = &$this->f;
            $info = &$this->f->page->info;

            if ($this->template == 'action') {
                echo '<h1>Action Error</h1>'; exit;
            } else if (!file_exists(PATH_ROOT.'_layouts/'.$this->template.'.php')) {
                echo '<h1>Template Error</h1>'; exit;
            } else {
                include PATH_ROOT.'_layouts/'.$this->template.'.php';
            }
        }

        final public function addToTitle($str) {
            $this->title .= ' '.trim($str);
        }
        final public function setTitle($str) {
            $this->title = $str;
        }
        final public function getTitle() {
            return $this->title;
        }

        public function HTMLaddHead() {

        }
        public function HTMLaddFooter() {
            return "        <!-- (".(microtime()-MICROTIME)." segs)-->\n";
        }
    }
?>