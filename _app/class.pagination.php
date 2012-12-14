<?
    class pagination {
        public $rs;

        private static $instance = false;
        private $manager;

        private $minPage;
        private $maxPage;
        private $current;
        private $initPage;
        private $numPages;

        public function __construct($sqlOrMaxpage, $current, $pageReg = 20, $numPages = 10) {
            pagination::$instance = $this;

            $this->manager = !empty($GLOBALS['MANAGER']);
            $this->minPage = 1;
            $this->maxPage = 0;
            $this->current = $current;

            if (!is_numeric($sqlOrMaxpage)) {
                $offset = ($this->current-1) * $pageReg;

                $sql = preg_replace('/^\s*SELECT(\s+SQL_CALC_FOUND_ROWS)?/', 'SELECT SQL_CALC_FOUND_ROWS ', $sqlOrMaxpage);
                $this->rs = DB::exec($sql.' LIMIT '.$offset.', '.$pageReg);

                if (!DB::error()) {
                    $this->maxPage = ceil(DB::field('SELECT FOUND_ROWS()') / $pageReg);
                }
            } else {
                $this->maxPage = $sqlOrMaxpage;
            }

            if ($this->current > $this->maxPage) {
                $this->current = $this->maxPage;
            }

            // num and init page
            if ($this->maxPage <= $this->numPages) {
                $this->numPages = $this->maxPage - 1;
                $this->initPage = $this->minPage;
            } else {
                $this->numPages = $numPages;
                $this->initPage = $this->current - ceil($numPages / 2);
                $this->initPage = max($this->initPage, $this->minPage);
                $this->initPage = min($this->initPage, $this->maxPage - $numPages);
            }
        }

        // PRIVATE

        private function link($uri, $num, $qs = '') {
            return $uri == '#/' ? '#' : func::link($uri.'/'.$num, null, $qs);
        }

        private function paging_site($numPages, $initPages, $uri, $qs) {
            $ret = '';
            if ($this->current > $this->minPage) {
                //$ret .= '<a href="'.$this->link($uri, $this->minPage, $qs).'" title="primeira">&laquo; primeira</a><span>...</span>';
                $ret .= '<a href="'.$this->link($uri, $this->current - 1, $qs).'" class="prev icon" title="anterior">Anterior</a>';
            }

            $ret .= '<div>';
            if ($numPages !== false) {
                for ($numPages, $initPages; $numPages >= 0; $numPages--, $initPages++) {
                    if ($initPages != $this->current) {
                        $ret .= '<a href="'.$this->link($uri, $initPages, $qs).'" title="página '.$initPages.'">'.$initPages.'</a>';
                    } else {
                        $ret .= '<span>'.$initPages.'</span>';
                    }
                }
            }
            $ret .= '</div>';

            if ($this->current < $this->maxPage) {
                $ret .= '<a href="'.$this->link($uri, $this->current + 1, $qs).'" class="next icon" title="próxima">Próxima</a>';
                //$ret .= '<a href="'.$this->link($uri, $this->maxPage, $qs).'" title="última">última &raquo;</a>';
            }

            return $ret;
        }

        private function paging_manager($numPages, $initPages, $uri, $qs) {
            $ret = '';
            if(!empty($qs)) {
                $qs .= '&amp;';
            }

            if ($this->current > $this->minPage) {
                $ret .= '<a href="'.$this->link($uri, '', $qs.'page='.$this->minPage).'" class="iFirst icon" title="primeira página">primeira</a>';
                if ($this->minPage != ($this->current - 1)) {
                    $ret .= '<a href="'.$this->link($uri, '', $qs.'page='.($this->current-1)).'" class="iPrev icon" title="página anterior">anterior</a>';
                }
            }

            if ($numPages !== false) {
                for ($numPages, $initPages; $numPages >= 0; $numPages--, $initPages++) {
                    if ($initPages != $this->current) {
                        $ret .= '<a href="'.$this->link($uri, '', $qs.'page='.$initPages).'" title="página '.$initPages.'">'.$initPages.'</a>';
                    } else {
                        $ret .= '<span>'.$initPages.'</span>';
                    }
                }
            }

            if ($this->current < $this->maxPage) {
                if ($this->maxPage != ($this->current + 1)) {
                    $ret .= '<a href="'.$this->link($uri, '', $qs.'page='.($this->current+1)).'" class="iNext icon" title="próxima página">próxima</a>';
                }
                $ret .= '<a href="'.$this->link($uri, '', $qs.'page='.$this->maxPage).'" class="iLast icon" title="última página">última</a>';
            }
            return $ret;
        }

        // PUBLIC

        public function paging($uri = '', $qs = '') {
            if ($this->maxPage == $this->minPage) {
                return false;
            }
            $ret = '<div id="pagination">';
            if ($this->manager) {
                $ret .= $this->paging_manager($this->numPages, $this->initPage, $uri, $qs);
            } else {
                $ret .= $this->paging_site($this->numPages, $this->initPage, $uri, $qs);
            }
            $ret .= '</div>';
            return $ret;
        }

        // INSTANCE PAGING
        public static function instance_paging($uri = '', $qs = '') {
            if (pagination::$instance) {
                return pagination::$instance->paging($uri, $qs);
            }
        }
    }
?>