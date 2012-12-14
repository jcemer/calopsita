<?
    class page extends fm_page {

        ## HTML PAGE ##
        public function HTMLpage() {
?>
    <div id="content">
        <img src="images/calopsita.png" alt="Calopsita">
        </nav>
        <p>
            Acesse o <a href="<?= func::link('manager', 0) ?>">gerenciador</a>.<br>
            Confira o <em>README.md</em> para mais informações.
        </p>
    </div>
<?
        }
    }
?>