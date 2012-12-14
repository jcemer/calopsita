<?
    if(!defined(INDEX)) exit;
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <title><?= $f->page->getTitle() ?></title>
        <meta charset="UTF-8">
        <meta name="Robots" content="noindex, nofollow" />
        <base href="<?= PATH_BASE_HREF ?>" />
        <link href="styles/manager/main.css" rel="stylesheet" />
        <link href="styles/manager/main_print.css" media="print" rel="stylesheet" />
        <link href="styles/manager/vendor/jquery-ui.css" rel="stylesheet" />
<?= $f->page->HTMLaddHead() ?>
    </head>
    <body id="<?=$f->getPageName()?>">
        <div id="top">
            <div class="inner">
                <h1 class="logo"><a href="<?= func::link('entrance', 1) ?>">Calopsita</a></h1>
<?
    if ($f->getPageName() != 'index') {
?>
                <div class="navigation">
                    <a href="<?= func::link('entrance', 1) ?>" class="home">Home</a>
                    <a href="<?= func::link('logout', 1) ?>" class="close">Sair</a>
                </div>
<?
    }
?>
            </div>
        </div>
        <div id="main">
            <div id="container" class="inner">
<?
    if ($f->getPageName() == 'index') {
        $f->page->HTMLpage();

    } else {
?>
            <div id="menu">
                <div>
                    <h2>Módulos</h2>
                    <?= manager::menu(array(
                        'event' => array('eventos', 'event')
                    ), $f->getPageName()) ?>
                </div>

                <div class="add">
                    <h2>Adicionais</h2>
                    <?= manager::menu(array(
                        'user_log' => array('acessos', 'user_log'),
                        'backup' => array('backup', 'backup'),
                        'user' => array('usuários', 'user')
                    ), $f->getPageName()) ?>
                </div>
            </div>

            <div id="content">
                <h2><?= $f->page->name_title ?></h2>
                <? $f->page->HTMLpage() ?>
                <div id="navigation" style="display:none">
                    <ul class="util">
                        <li><a href="#" onclick="history.go(-1); return false" class="btn btn-back" title="Voltar para a página anterior">voltar</a></li>
                        <li><a href="#container" class="btn btn-top" title="Ir para o topo da página">topo</a></li>
                        <li><a href="#" onclick="window.print(); return false" class="btn btn-simple" title="Imprimir">imprimir</a></li>
                    </ul>
                    <?= pagination::instance_paging('', $GLOBALS['MANAGER']->qsStrPagination()) ?>
                </div>
            </div>
<?
    }
?>
            </div>
            <div id="footer" style="display:none">
                <div class="inner">
                    <a href="http://github.com/jcemer/calopsita" target="_blank" title="Calopsita">Calopsita</a>
                </div>
            </div>
        </div>

        <script src="scripts/manager/vendor/jquery.js"></script>
        <script src="scripts/manager/vendor/jquery-ui.js"></script>
        <script src="scripts/manager/vendor/jquery.masked-input.js"></script>
        <script src="scripts/manager/main.js"></script>
<?= $f->page->HTMLaddFooter() ?>
    </body>
</html>