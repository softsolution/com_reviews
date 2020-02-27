<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }
	header('Content-Type: text/html; charset=windows-1251');
	session_start();

    if (!isset($_REQUEST['module_id'])) { die(2); }

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    // Грузим ядро и классы
    include(PATH.'/core/cms.php');

    // Грузим конфиг
    include(PATH.'/includes/config.inc.php');
    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('page');
    $inDB   = cmsDatabase::getInstance();


    // Грузим шаблонизатор
    $smarty = $inCore->initSmarty();

    // Входные данные
    $module_id	= $inCore->request('module_id', 'int', '');

    $cfg = $inCore->loadModuleConfig($module_id);

        if (!isset($cfg['reviewscount'])) {
        $cfg['reviewscount'] = 1;
    }
    if (!isset($cfg['maxlen'])) {
        $cfg['maxlen'] = 0;
    }
    if (!isset($cfg['showlink'])) {
        $cfg['showlink'] = 0;
    }

    $is_reviews = false;


    $sql = "SELECT r.*
		FROM cms_reviews r
                WHERE r.published = 1 ORDER BY RAND() LIMIT " . $cfg['reviewscount'];

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)) {

        $is_reviews = true;

        $reviews = array();
        while ($review = $inDB->fetch_assoc($result)) {

            //$review['pubdate'] = $inCore->dateFormat($review['pubdate'], true, false);

            $review['description'] = nl2br($review['description']);
            if ($cfg['maxlen'] && strlen($review['description']) > $cfg['maxlen']) {
                $review['description'] = substr($review['description'], 0, $cfg['maxlen']) . '...';
            }

            $reviews[] = $review;
        }
    }


    // Отдаем в шаблон
    ob_start();
    $smarty = $inCore->initSmarty('modules', 'mod_reviews_random_entry.tpl');
    //$smarty->assign('sid', md5(session_id()));
    $smarty->assign('reviews', $reviews);
    $smarty->assign('is_reviews', $is_reviews);
    $smarty->assign('module_id', $module_id);
    $smarty->assign('cfg', $cfg);
    $smarty->display('mod_reviews_random_entry.tpl');

    $html = ob_get_clean();
    echo $html;

?>