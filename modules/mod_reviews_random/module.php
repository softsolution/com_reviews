<?php

/* ================================================== */
/*            created by soft-solution.ru           */
/* ================================================== */

function mod_reviews_random($module_id) {

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

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


    $sql = "SELECT r.* FROM cms_reviews r WHERE r.published = 1 ORDER BY RAND() LIMIT " . $cfg['reviewscount'];

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)) {

        $is_reviews = true;

        $reviews = array();
        while ($review = $inDB->fetch_assoc($result)) {

            $review['description'] = nl2br($review['description']);
            if ($cfg['maxlen'] && strlen($review['description']) > $cfg['maxlen']) {
                $review['description'] = substr($review['description'], 0, $cfg['maxlen']) . '...';
            }

            $reviews[] = $review;
        }
    }

    $smarty = $inCore->initSmarty('modules', 'mod_reviews_random.tpl');
    $smarty->assign('reviews', $reviews);
    $smarty->assign('is_reviews', $is_reviews);
    $smarty->assign('module_id', $module_id);
    $smarty->assign('cfg', $cfg);
    $smarty->display('mod_reviews_random.tpl');

    return true;
}

?>