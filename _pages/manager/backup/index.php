<?
        class page extends fm_page{
                public $template = 'manager';
                public $name = 'backup';
                public $name_title = 'Backup';

                public function initialize(){
                        require_once(PATH_APP.'manager/bootstrap.php');
                        $GLOBALS['MANAGER']->logged($this);
                }

                ## HTML PAGE ##
                public function HTMLpage(){
                        manager::registre($this, false);
                }

                ## REGISTRE ##
                public function registre(){
                        $rs = DB::exec('SELECT date, user, size FROM backup ORDER BY date DESC LIMIT 3');
?>
        <form action="<?= func::link('backup') ?>" method="post" >
                <div class="row">
                        <fieldset>
                                <h4>Faça aqui o backup dos seus dados</h4>
                                <p>Para assegurar seus dados é recomendado que você faça o backup regularmente.</p>
                                <p>Em caso de danos a base de dados, o último arquivo gerado <em>sem erros</em> poderá ser restaurado, permanecendo os registros anteriores a sua criação.</p>
                                <p class="footer">
                                        <button type="submit" class="btn">backup</button>
                                </p>
                        </fieldset>
                        <fieldset>
                                <h4>Últimos três backups</h4>
                                <? if ($rs->num()) { ?>
                                <table class="roll">
                                        <thead><tr>
                                                <th style="width:80px">data</th>
                                                <th>usuário</th>
                                                <th style="width:80px">tamanho</th>
                                        </tr></thead>
                                        <tbody>
                                        <? while($row = $rs->row()) { ?>
                                                <tr>
                                                        <td><?= func::dttm2br($row['date']) ?></td>
                                                        <td><?= func::row($row, 'user') ?></td>
                                                        <td><?= func::size($row['size']) ?></td>
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