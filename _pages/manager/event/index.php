<?
    class page extends fm_page {
        public $template = 'manager';
        public $name = 'event';
        public $name_title = 'Calendário de eventos';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $GLOBALS['MANAGER']->checkOrder(
                array('date' => 'data', 'description' => 'evento', 'active' => 'ativo'),
                'date', 'DESC'
            );
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::panel('<a href="'.func::link('edit', null, $GLOBALS['MANAGER']->qsStr()).'" class="btn-insert btn">inserir evento</a>');

            if ($search = $GLOBALS['MANAGER']->getQsExtSQL('search')) {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS id, date, description, active FROM event WHERE description LIKE "%'.$search.'%" '.$GLOBALS['MANAGER']->orderSql;
            } else {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS id, date, description, active FROM event '.$GLOBALS['MANAGER']->orderSql;
            }
            manager::roll($this, $sql);
        }

        public function roll(&$rs) {
?>
    <thead><tr>
        <th class="icon">alterar</th>
        <th style="width:90px"><?= $GLOBALS['MANAGER']->linkOrder(0) ?></th>
        <th><?= $GLOBALS['MANAGER']->linkOrder(1) ?></th>
        <th style="width:90px"><?= $GLOBALS['MANAGER']->linkOrder(2) ?></th>
        <th class="icon">excluir</th>
    </tr></thead>
    <tbody>
        <? while($row = $rs->row()) { ?>
        <tr>
            <td><a href="<?= func::link('edit/'.$row['id'], null, $GLOBALS['MANAGER']->qsStr()) ?>" class="icon-change icon" title="Alterar">Alterar</a></td>
            <td class="date"><?= func::dt2br($row['date']) ?></td>
            <td class="description"><?= func::row($row, 'description') ?></td>
            <td><?= $row['active'] ? 'Sim' : '<strong>Não</strong>' ?></td>
            <td><a href="<?= func::link('delete/'.$row['id'], null, $GLOBALS['MANAGER']->qsStr()) ?>" class="icon-delete icon" title="Excluir">Excluir</a></td>
        </tr>
        <? } ?>
    </tbody>
<?
        }
    }
?>