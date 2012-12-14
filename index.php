<?
    ## MYSQL ##
    define('DB_CONN', true);
    define('DB_DRIVER', 'mysql');
    define('DB_HOST', '');
    define('DB_USER', '');
    define('DB_PWD',  '');
    define('DB_DATA', '');

    ## MANAGER ##
    define('USER_CONTROL', 'MANAGER');

    ## PAGE ##
    define('PAGE_TITLE', 'Calopsita');

    ## PATH ##
    define('URL_REWRITE', true);
    define('PATH_ROOT', dirname(__FILE__).'/');
    define('PATH_APP', PATH_ROOT.'_app/');
    define('PATH_PAGES', PATH_ROOT.'_pages/');
    define('PATH_UPLOAD', PATH_ROOT.'upload/');

    define('PATH_INVALID', 'calopsita/');
    define('PATH_BASE_HREF', 'http://localhost/calopsita/');

    ## ERROR #
    define('ERROR_DEBUG', 'On'); // On | Off
    define('ERROR_RSS_TITLE', '');
    define('ERROR_RSS_LINK', '');
    define('ERROR_RSS_PASS', '');
    define('PATH_FILE_ERROR', PATH_ROOT.'_log/.error_log');

    define('INDEX', 'true');
    require_once(PATH_APP.'bootstrap.php');
?>