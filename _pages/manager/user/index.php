<?
    class page extends fm_page {
        public $template = 'manager';
        public $name = 'user';
        public $name_title = 'Usuários';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $GLOBALS['MANAGER']->checkOrder(
                array('description' => 'nome', 'login' => 'login'),
                'description', 'ASC'
            );
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::panel('<a href="'.func::link('edit', null, $GLOBALS['MANAGER']->qsStr()).'" class="btn-insert btn" title="inserir">inserir usuário</a>');

            if ($search = $GLOBALS['MANAGER']->getQsExtSQL('search')) {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS id, description, login FROM user WHERE id != 1 AND (description LIKE "%'.$search.'%" OR login LIKE "%'.$search.'%") '.$GLOBALS['MANAGER']->orderSql;
            } else {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS id, description, login FROM user WHERE id != 1 '.$GLOBALS['MANAGER']->orderSql;
            }
            manager::roll($this, $sql);
        }

        public function roll(&$rs) {
?>
    <thead><tr>
        <th class="icon">alterar</th>
        <th style="width:200px"><?= $GLOBALS['MANAGER']->linkOrder(0) ?></th>
        <th><?= $GLOBALS['MANAGER']->linkOrder(1) ?></th>
        <th class="icon">excluir</th>
    </tr></thead>
    <tbody>
        <? while($row = $rs->row()) { ?>
        <tr>
            <td><a href="<?= func::link('edit/'.$row['id'], null, $GLOBALS['MANAGER']->qsStr()) ?>" class="icon-change icon" title="Alterar">Alterar</a></td>
            <td><?= func::row($row, 'description') ?></td>
            <td><?= func::row($row, 'login') ?></td>
            <td><a href="<?= func::link('delete/'.$row['id'], null, $GLOBALS['MANAGER']->qsStr()) ?>" class="icon-delete icon" title="Excluir">Excluir</a></td>
        </tr>
        <? } ?>
    </tbody>
<?
        }
    }
?>