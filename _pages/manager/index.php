<?
    class page extends fm_page {
        public $template = 'manager';
        /* change */
        public $name = 'index';
        public $name_title = 'Login';

        public function initialize(){
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->loggedIndex($this);
        }

        ## HTML ADD FOOTER ##
        public function HTMLaddFooter() {
?>
        <script type="text/javascript">
            $("#msg0").hide();
            $("#registre").show();
        </script>
<?
        }

        ## HTML PAGE ##
        public function HTMLpage() {
?>

    <div id="content">
        <?= func::msg('msg0', array('<noscript><ul><li>O Javascript deve ser ativado para que você possa acessar o gerenciador.</li></ul></noscript>')) ?>
        <?= func::msg('msg1', array(func::getCheck('msg'))) ?>
        <form id="registre" style="display:none" action="<?= func::link('login') ?>" method="post">
            <div class="row">
                <fieldset>
                    <p>
                        <label for="login">Login:</label>
                        <input id="login" name="login" class="field g" type="text" />
                    </p>
                    <p>
                        <label for="password">Senha:</label>
                        <input id="password" name="password" class="field g" type="password" />
                    </p>
                </fieldset>
            </div>
            <p class="footer">
                <button type="submit" class="btn">acessar</button>
            </p>
        </form>
    </div>
<?
        }
    }
?>