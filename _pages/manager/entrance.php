<?
    class page extends fm_page {
        public $template = 'manager';
        /* change */
        public $name = 'entrance';
        public $name_title = 'Bem vindo';

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::registre($this, false);
        }

        ## REGISTRE ##
        public function registre() {
            $backup = DB::row('SELECT date, user, size FROM backup ORDER BY date DESC LIMIT 3');
            $logs = DB::exec('SELECT description, date, action FROM user_log WHERE description = "'.$GLOBALS['MANAGER']->getName().'" ORDER BY date DESC LIMIT 4');
?>
    <form action="<?= func::link('backup') ?>">
        <div class="row">
            <fieldset>
                <h4>Gerencie</h4>
                <p><strong><?= $GLOBALS['MANAGER']->getName() ?></strong>, no menu ao lado você tem acesso a todo o conteúdo gerenciável do seu website.</p>
                <p>Fique a vontade, altere a qualquer momento tudo o que você desejar.</p>

                <h4>Backup dos dados</h4>
                <p>O último backup dos dados foi feito em <strong><?= func::dt2br($backup['date']) ?></strong> por <strong><?= func::row($backup, 'user') ?></strong>.</p>
                <p>É aconselhavel fazer o backup dos dados semanalmente. Se desejar, <a href="<?= func::link('backup') ?>" title="Backup">clique aqui</a> e faça agora mesmo.</p>
            </fieldset>
            <fieldset>
                <h4>Seus últimos acessos</h4>
                <? if($logs->num()){ ?>
                <table class="roll">
                    <thead><tr>
                        <th style="width:80px">data</th>
                        <th>usuário</th>
                        <th style="width:80px">ação</th>
                    </tr></thead>
                    <tbody>
                    <? while ($log = $logs->row()) { ?>
                        <tr>
                            <td><?= func::dttm2br($log['date']) ?></td>
                            <td><?= func::row($log, 'description') ?></td>
                            <td><?= ($log['action'] == 'E' ? 'acessou' : 'saiu') ?></td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
                <? } ?>
            </fieldset>
        </div>
    </form>
<?
        }
    }
?>