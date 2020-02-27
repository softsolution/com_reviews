<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

function mod_reviews_form($module_id){

	$inCore = cmsCore::getInstance();
	$cfg_com = $inCore->loadComponentConfig('reviews');

	$smarty = $inCore->initSmarty('modules', 'mod_reviews_form.tpl');
	$smarty->assign('module_id', $module_id);
	$smarty->assign('item', $item);
	$smarty->assign('sid', md5(session_id()));
	$smarty->assign('cfg_com', $cfg_com);
	$smarty->display('mod_reviews_form.tpl');

	return true;
}

?>