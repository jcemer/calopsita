<?
    if(!defined(INDEX)) exit;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?=$f->page->getTitle()?></title>

    <base href="<?= PATH_BASE_HREF;?>" />
    <style type="text/css" media="print">
        * {
            margin: 0;
            padding: 0;
        }
        #info {
            display: none;
        }
    </style>
<?= $f->page->HTMLaddHead() ?>
</head>
<body>
    <div id="info">
        <h1>Instruções para impressão</h1>
        <ul>
            <li>Para imprimir utilize a opção de impressão do seu navegador ou <a href="javascript:window.print()" title="imprimir">clique aqui</a></li>
            <li>Configure a impressora para modo <em>normal</em> de impressão (não usar opção <em>rascunho</em>)</li>
            <li>Imprimir em folha A4 (210x297 mm)</li>
        </ul>
    </div>
    <div id="main">
<?= $f->page->HTMLmain() ?>
    </div>
</body>
</html>