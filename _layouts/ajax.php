<?
    if(!defined(INDEX)) exit;

    header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-type: text/html; charset=utf-8');

    echo $f->page->AJAXcontent();
?>