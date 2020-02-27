<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* module.php of module reviews mod_reviews InstantCMS 1.10                                   */
/* ****************************************************************************************** */

function mod_reviews($module_id, $cfg){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

	if (!isset($cfg['count'])) { $cfg['count']= 5; }
        if (!isset($cfg['category_id'])) { $cfg['category_id']= -1; }
        if (!isset($cfg['view_type'])) { $cfg['view_type']= "slider"; }
        if (!isset($cfg['autoSlide'])) { $cfg['autoSlide']= 1; }
        if (!isset($cfg['SlideInterval'])) { $cfg['SlideInterval']= 5000; }
        if (!isset($cfg['nav'])) { $cfg['nav']= 'top'; }
        if (!isset($cfg['autoHeight'])) { $cfg['autoHeight'] = 1;}
        if (!isset($cfg['effect'])) { $cfg['effect'] = 'default';}
        if (!isset($cfg['arrow'])) { $cfg['arrow'] = 0;}
        if (!isset($cfg['showlink'])) { $cfg['showlink']= 1; }
        if (!isset($cfg['showdate'])) { $cfg['showdate']= 1; }
        if (!isset($cfg['is_pag'])) { $cfg['is_pag']= 0; }

        $is_reviews = false;

	// опции постраничной разбивки
	$page    = 1;
	$perpage = $cfg['reviewscount'];

	$sql = "SELECT r.* FROM cms_reviews r WHERE r.target = 'review' AND r.published = 1";
        if($cfg['category_id']!=-1){
            $sql .= " AND r.category_id = ".$cfg['category_id'];
        }
        $sql .= " ORDER BY r.pubdate DESC LIMIT ".$cfg['count'];

        $result = $inDB->query($sql);

        // Считаем общее количество отзывов если опция пагинация включена
	if ($cfg['is_pag']) {
            $sql_total = "SELECT 1 FROM cms_reviews WHERE published = 1 AND target = 'review'";
            if($cfg['category_id']!=-1){
                $sql .= " AND category_id = ".$cfg['category_id'];
            }
            $result_total = $inDB->query($sql_total) ;
            $total_page = $inDB->num_rows($result_total);
	}

	if ($inDB->num_rows($result)){

            $is_reviews = true;
            $reviews = array();
            
            while($review = $inDB->fetch_assoc($result)){

                $review['pubdate']     = $inCore->dateFormat($review['pubdate'], true, false, false);
                $review['description'] = nl2br($review['description']);
                if ($cfg['maxlen'] && mb_strlen($review['description'])>$cfg['maxlen']) {
                    $review['description'] = mb_substr($review['description'], 0, $cfg['maxlen']). '...';
                 }
                $reviews[] = $review;
            }
        }
        
        global $_CFG;
        
        cmsPage::initTemplate('modules', 'mod_reviews')->
            assign('reviews', $reviews)->
            assign('is_reviews', $is_reviews)->
            assign('template', $_CFG['template'])->
            assign('mid', $module_id)->
            assign('cfg', $cfg)->
            assign('pagebar_module', (isset($cfg['is_pag']) ? cmsPage::getPagebar($total_page, $page, $perpage, 'javascript:reviewsPage(%page%, '.$module_id.')') : ''))->
            display('mod_reviews.tpl');

	return true;
}

?>