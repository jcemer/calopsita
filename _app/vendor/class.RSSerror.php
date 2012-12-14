<?php

class RRSerror{
        private $args  = array();
        private $param = array();
        private $xml = "";

        public function __construct($args, $param){
                $this->args  = $args;
                $this->param = $param;

                //It opens the log archive
                $file_log = @file($this->args['log']);
                //It verify if log file is empty
                if($file_log){
                        //This makes an array from the log file
                        $file_log = array_reverse($file_log);
                        $size = (count($file_log) >= 10)? 10 : count($file_log);
                        for($i=0; $i<$size; $i++){
                                $error[$i]["title"]       = $this->title($file_log[$i]);
                                $error[$i]["description"] = $this->description($file_log[$i]);
                                $error[$i]["pubDate"]     = $this->data($file_log[$i]);
                                $error[$i]["link"]        = $this->args["url"];
                        }
                }
                if(!isset($error)){
                        $error[0]["title"]       = "All clear";
                        $error[0]["description"] = "No error detected yet.";
                        $error[0]["pubDate"]     = time();
                        $error[0]["link"]        = $this->args["url"];
                }
                //RSS setup parameters Attribution
                $atrbs = array(
                        "encoding"    => $this->param["encoding"],
                        "language"    => $this->param["language"],
                        "title"       => $this->param["title"],
                        "description" => $this->param["description"],
                        "link"        => $this->param["link"],
                        "items"       => $error
                );

                $this->xml = easyRSS::rss($atrbs);
        }

        /* TITLE */
        private function title($line){
                preg_match("/(PHP[^:]*):/", $line, $result);
                return $result[1];
        }

        /* DESCRIPTION */
        private function description($line){
                preg_match("/(?:\] PHP.*?:  )(.*)/", $line, $result);
                preg_match("/on line ([0-9]*)/", $line, $line_n);
                $line_n = $line_n[1];

                $output  = '<p><strong>'.$result[1].'</strong></p>'."\n";

                if(preg_match('/([^ ]+\.php) on line '.$line_n.'/', $line, $file)){
                                $source  = @file($file[1]);
                                if($source){
                                        $begin   = (($line_n - $this->args['lines']) < 1)? 1 : ($line_n - $this->args['lines']);
                                        $end     = (($line_n + $this->args['lines']) > count($source)+1)? count($source)+1 : ($line_n + $this->args['lines']);

                                        $output  .='<pre>'."\n";
                                        for($i = $begin; $i < $end; $i++)
						$output .=	($line_n == $i?'<strong>['.$i.']</strong>':'['.$i.']').
                                                        '&nbsp;'.htmlentities($source[$i-1]);
                                        $output .= "</pre>";

                                }else{
                                        $output = "Sorry! I can't show source code now.";
                                }
                }else{
                                 $output = "Sorry! I can't show source code now.";
                }
                return $output;
        }

        /* DATA */
        private function data($line){
                preg_match("/\[([^]]*)\]/", $line, $result);
                $datetime = split(" ", $result[1]);
                $hour = split(":", $datetime[1]);
                $time = (strtotime($datetime[0]) + ($hour[0]*3600) + ($hour[1]*60) + $hour[2]);
                return $time;
        }

        /* GET XML */
        public function getXML(){
                return $this->xml;
        }
}
?>