<?
    class page extends fm_page {
        public $template = 'manager';
        /* change */
        public $name = 'user_log';
        public $name_title = 'Acessos';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $GLOBALS['MANAGER']->checkOrder(
                array('date' => 'data', 'description' => 'usuário', 'action' => 'ação'),
                'date', 'DESC'
            );
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::panel();
            if ($search = $GLOBALS['MANAGER']->getQsExtSQL('search')) {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS description, date, action FROM user_log WHERE description LIKE "%'.$search.'%" '.$GLOBALS['MANAGER']->orderSql;
            } else {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS description, date, action FROM user_log '.$GLOBALS['MANAGER']->orderSql;
            }
            manager::roll($this, $sql);
        }

        public function roll(&$rs) {
?>
    <thead><tr>
        <th style="width:140px"><?= $GLOBALS['MANAGER']->linkOrder(0) ?></th>
        <th><?= $GLOBALS['MANAGER']->linkOrder(1) ?></th>
        <th style="width:140px"><?= $GLOBALS['MANAGER']->linkOrder(2) ?></th>
    </tr></thead>
    <tbody>
        <? while($row = $rs->row()) { ?>
        <tr>
            <td><?= func::dttm2br($row['date']) ?></td>
            <td><?= func::row($row, 'description') ?></td>
            <td><?= ($row['action'] == 'E' ? 'acessou' : 'saiu') ?></td>
        </tr>
        <? } ?>
    </tbody>
<?
        }
    }
?>