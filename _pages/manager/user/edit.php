<?
    class page extends fm_page {
        public $template = 'manager';
        public $name = 'user';
        public $name_title = 'Usuários';

        public $page_id = 0;
        public $page_row = 0;

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $this->page_id = intval($this->f->pathURIAt(0));
            if ($this->page_id) {
                $this->page_row = DB::row('SELECT id, created, created_by, modified, modified_by, description, login, active FROM user WHERE id = '.$this->page_id);
            }
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::registre($this);
        }

        ## REGISTRE ##
        public function registre() {
?>
    <div class="row">
        <fieldset>
            <h4>Informações</h4>
            <? if(!$this->page_id) { ?>
            <p>
                <label for="description">Nome*:</label>
                <input name="description" id="description" title="Nome" class="field gg" type="text" maxlength="50" value="<?= func::postOrRow($this->page_row, 'description') ?>" />
            </p>
            <? } else { ?>
            <p>
                <span class="label">Nome:</span>
                <span class="field"><?= func::row($this->page_row, 'description') ?></span>
            </p>
            <? } ?>
            <p>
                <label for="login">Login*:</label>
                <input name="login" id="login" title="Login" class="field m" type="text" maxlength="20" value="<?= func::postOrRow($this->page_row, 'login') ?>" />
            </p>
            <? if($this->page_id) { ?>
            <p>
                <input name="password_change" id="password_change" title="Alterar Senha" value="1" type="checkbox"/>
                <label for="password_change">Alterar a senha</label>
            </p>
            <? } ?>

            <div id="passwords">
                <p class="side m">
                    <label for="password">Senha:</label>
                    <input name="password" id="password" title="Senha" class="field m" type="password" />
                </p>
                <p class="side m">
                    <label for="password_confirm">Repetir senha:</label>
                    <input name="password_confirm" id="password_confirm" title="Confirmação de senha" class="field m" type="password" />
                </p>
            </div>

            <? if($this->page_id) { ?>
            <script type="text/javascript">
                if (!$("#password_change:checked").length) {
                    $("#passwords").hide();
                }
                $("#password_change").click(function () {
                    $("#passwords").toggle();
                });
            </script>
            <? } ?>
        </fieldset>

        <!-- status -->
        <fieldset>
            <h4>Situação do registro</h4>
            <?= manager::registreInfo($this->page_row) ?>
            <p>
                <input name="active" id="active" title="Ativo" value="1" type="checkbox" <?= func::postOrRowCheck($this->page_row, 'active', '1', '1') ?> />
                <label for="active">Ativo</label>
            </p>
        </fieldset>
    </div>
<?
        }
    }
?>