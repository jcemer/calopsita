<?
        class action extends fm_page{
                public $template = 'action';

                public function initialize(){
                        require_once(PATH_APP.'manager/bootstrap.php');
                        require_once(PATH_APP.'vendor/class.mysql_dump.php');
                        $GLOBALS['MANAGER']->logged($this);

                        $mysql_dump = new MYSQL_DUMP(DB_HOST, DB_USER, DB_PWD);
                        if (!$backup = $mysql_dump->dumpDB('`'.DB_DATA.'`')) {
                                echo $mysql_dump->error();
                        } else {
                                $ret = $mysql_dump->download_sql($backup, "backup_".date('Y-m-d_').time().".sql");
                                DB::exec('INSERT INTO backup (date, user, size) VALUES(NOW(), "'.$GLOBALS['MANAGER']->getName().'", '.strlen($ret).')');
                                echo $ret;
                        }
                }
        }
?>