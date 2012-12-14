<?
    if(!defined(INDEX)) exit;
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title><?= $f->page->getTitle() ?></title>
        <base href="<?= PATH_BASE_HREF ?>">
        <link rel="stylesheet" href="styles/main.css">
    <?= $f->page->HTMLaddHead() ?>
    </head>
    <body id="<?=$f->getPageName()?>">
        <? $f->page->HTMLpage() ?>
        <?= $f->page->HTMLaddFooter() ?>
    </body>
</html>