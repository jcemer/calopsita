<?
    class page extends fm_page {
        public $template = 'manager';
        public $name = 'event';
        public $name_title = 'Calendário de Eventos';

        public $page_id = 0;
        public $page_row = 0;

        public function initialize() {
            require_once(PATH_APP.'manager/bootstrap.php');
            $GLOBALS['MANAGER']->logged($this);

            $this->page_id = intval($this->f->pathURIAt(0));
            if ($this->page_id) {
                $this->page_row = DB::row('SELECT id, created, created_by, modified, modified_by, date, schedule, description, place, locale, text, img0_width, img0_height, img0_file, photo_width, photo_height, photo_file, active FROM event WHERE id = '.$this->page_id);
            }
        }

        ## HTML PAGE ##
        public function HTMLpage() {
            manager::registre($this);
        }

        public function registre() {
?>
    <div class="row">
        <fieldset>
            <h4>Informações</h4>
            <p>
                <label for="description">Evento*:</label>
                <input name="description" id="description" class="field gg" value="<?= func::postOrRow($this->page_row, 'description') ?>" maxlength="35" />
            </p>
            <div class="modular">
                <p class="side m">
                    <label for="schedule">Datas*: <span>De 22/04 à 28...</span></label>
                    <input name="schedule" id="schedule" class="field m" value="<?= func::postOrRow($this->page_row, 'schedule') ?>" />
                </p>
                <p class="side m">
                    <label for="date">Data de início*:</label>
                    <input name="date" id="date" class="field mp" value="<?= func::dt2br(func::postOrRow($this->page_row, 'date')) ?>" />
                </p>
            </div>
            <h4>Endereço</h4>
            <p>
                <label for="place">Local:<span>Hotel Palace</span></label>
                <input name="place" id="place" class="field gg" value="<?= func::postOrRow($this->page_row, 'place') ?>" />
            </p>
            <p>
                <label for="locale">Cidade e Estado:<span>Porto Alegre - RS</span></label>
                <input name="locale" id="locale" class="field gg" value="<?= func::postOrRow($this->page_row, 'locale') ?>" />
            </p>
        </fieldset>

        <fieldset>
            <h4>Logotipo</h4>
            <? if($this->page_row['img0_width']) { ?>
            <div class="image image-one">
                <img src="upload/event/<?= func::row($this->page_row, 'img0_file') ?>" width="200" alt="" />
            </div>
            <? } ?>
            <p>
                <label for="image">Imagem:</label>
                <input id="image" name="image" class="field-file" type="file" />
            </p>
        </fieldset>
    </div>

    <h3>Informações Gerais</h3>
    <div class="row">
        <fieldset>
            <h4>Resenha</h4>
            <p>
                <textarea name="text" id="text" class="field gg"><?= func::postOrRow($this->page_row, 'text') ?></textarea>
            </p>
        </fieldset>
        <fieldset>
            <h4>Foto</h4>
            <? if($this->page_row['photo_width']) { ?>
            <div class="image image-one">
                <img src="upload/event/<?= func::row($this->page_row, 'photo_file') ?>" width="200" alt="" />
                <div class="icons"><a href="#" class="btn-delete btn" title="excluir">excluir</a></div>
                <input name="photo_delete" type="hidden" />
            </div>
            <? } ?>
            <p>
                <label for="photo">Imagem:</label>
                <input id="photo" name="photo" class="field-file" type="file" />
            </p>
        </fieldset>
    </div>
    <div class="row">
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