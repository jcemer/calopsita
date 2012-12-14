<?
        if(!defined(INDEX)) exit;
        session_start();

        define('MICROTIME', microtime());

        // CLASS
        require_once(PATH_APP.'/class.func.php');
        require_once(PATH_APP.'/class.helpers.php');
        require_once(PATH_APP.'/class.pagination.php');
        require_once(PATH_APP.'/class.framework.php');
        require_once(PATH_APP.'/class.framework_page.php');
        require_once(PATH_APP.'/class.database.php');

        ## FRAMEWORK ##
        new framework();
?>