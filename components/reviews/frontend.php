<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component reviews for InstantCMS 1.10.4                                    */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */
if (!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function reviews($target='review', $target_id=0, $labels=array()) {

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inConf = cmsConfig::getInstance();

    // Проверяем включен ли компонент
    if(!$inCore->isComponentEnable('reviews')) { return false; }
    $cfg = $inCore->loadComponentConfig('reviews');

    global $_LANG;

    cmsCore::loadModel('reviews');
    $model = new cms_model_reviews($labels);
    
    $model->initAccess();

    $user_id   = $inUser->id;
    $is_admin  = $inUser->is_admin;

    $id        = $inCore->request('id', 'int', 0);
    $do        = $inCore->do;
    $page      = $inCore->request('page', 'int', 1);
    $cfg       = $model->config;
    
    //Подключаем CSS к странице
    $inPage->addHeadCSS('templates/'.$inConf->template.'/css/reviews.css');
    $inPage->addHeadJS('components/reviews/js/reviews.js');

/* ==================================================================================================== */
/* ========================== ЛЕНТА ОТЗЫВОВ ====================================================== */
/* ==================================================================================================== */

    if ($do == 'view') {

        $inPage->setTitle($_LANG['REVIEWS']);
	$inPage->addPathway($_LANG['REVIEWS']);

        $sql = "SELECT r.*
		FROM cms_reviews r
                WHERE r.target = '{$target}' AND r.target_id = '{$target_id}'";
        if (!$is_admin)    { $sql .= " AND r.published = 1"; }
        $sql .= " ORDER BY r.pubdate DESC
                LIMIT ".($page - 1)*$perpage.", " .$perpage;

        $result = $inDB->query($sql);

        //для корректной пагинации считаем количество отдельно
        $sql2 = "SELECT 1 FROM cms_reviews r";
        if (!$is_admin)    { $where .= " AND r.published = 1"; }
        if ($where) $sql2 .= "WHERE r.target = '{$target}' AND r.target_id = '{$target_id}'" . $where;

        $result_total = $inDB->query($sql2);
        $records = $inDB->num_rows($result_total);

        if ($inDB->num_rows($result)) {
            $reviews = array();
            while ($review = $inDB->fetch_assoc($result)) {
                $review['pubdate']     = $inCore->dateFormat($review['pubdate'], true, false, false);
                $review['description'] = nl2br($review['description']);
                $review['link']        = autoLink($review['link']);
                $reviews[] = $review;
            }
            $is_review = true;
        } else {
            $is_review = false;
        }

        $pagebar = cmsPage::getPagebar($records, $page, $perpage, '/reviews/page-%page%');

        cmsPage::initTemplate('components', 'com_reviews_view')->
            assign('is_admin',  $is_admin)->
            assign('user_id', $user_id)->
            assign('cfg', $cfg)->
            assign('reviews', $reviews)->
            assign('pagebar', $pagebar)->
            assign('is_review', $is_review)->
            assign('is_can_success_auction', $is_can_success_auction)->
            display('com_reviews_view.tpl');

    }
    
/* ==================================================================================================== */
/* ========================== ОТЗЫВЫ ПРИКРЕПЛЕННЫЕ К КОМПОНЕНТУ ======================================= */
/* ==================================================================================================== */

    if (!in_array($do, array('add', 'edit', 'delete')) && $target && $target_id){

        $inDB->where("r.target = '{$target}' AND r.target_id = '{$target_id}'");
        $total = $model->getReviewsTotal($inUser->is_admin);
        $reviews = $model->getReviews($inUser->is_admin);

        cmsPage::initTemplate('components', 'com_reviews_view')->
            assign('reviews_count', $total)->
            assign('target', $target)->
            assign('target_id', $target_id)->
            assign('is_admin', $is_admin)->
            assign('user_id', $user_id)->
            assign('cfg', $cfg)->
            assign('reviews', $reviews)->
            assign('pagebar', $pagebar)->
            assign('is_review', $is_review)->
            assign('labels', $model->labels)->
            assign('can_add_review', $model->can_add_review)->
            assign('add_review_js', "addReview('".$target."', '".$target_id."')")->
            display('com_reviews_view.tpl');
    }

/* ==================================================================================================== */
/* ========================== ДОБАВЛЯЕМ ОТЗЫВ ================================================== */
/* ==================================================================================================== */

    if ($do == 'add') {

        //если не авторизован, перебрасываем на ссылку для авторизации
        if (!$inUser->id && !$cfg['guest_enabled']){ cmsUser::goToLogin(); }
        
        $errors = '';
        
        //флаг, что пришла форма
        $is_submit = $inCore->inRequest('description');
        
        //если форма пришла:
        if($is_submit){
            
            //отрабатываем ограничения:
            //1. Количество отзывов в сутки
            if ($cfg['amount']!=0 && !$is_admin){
                $user_ip = $inUser->ip;
                $amount_today = $inDB->rows_count('cms_reviews', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

                if($cfg['amount']<=$amount_today){
                    cmsCore::addSessionMessage($_LANG['NO_ADD_TODAY'], 'info');
                    $inCore->redirect('/reviews');
                }
            }
            
            //2. Правильность каптчи
            if ($is_submit && !$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $errors .= $_LANG['ERR_CAPTCHA']; }
            
            //данные формы
            $item = array();
            $item['published']     = ($inUser->is_admin || ($cfg['guest_publish'] && !$user_id) ||  ($cfg['user_publish'] && $user_id)) ? 1 : 0;
            $item['name']          = $inCore->request('name', 'str');
            $item['phone']         = $inCore->request('phone', 'str');
            $item['email']         = $inCore->request('email', 'str');
            
            $item['link']          = $inCore->request('link', 'str');
            $item['description']   = $inCore->request('description', 'str');
            $item['category_id']   = $inCore->request('category_id', 'int', 0);
            
            //валидация данных
            $validation = array();
            if(!$item['description'] || mb_strlen($item['description'])<10) {$validation['description']=1; $errors .= $_LANG['ERR_DESC'];}
            if(!$item['name']) {$validation['name']=1; $errors .= $_LANG['ERR_NAME'];}
        }

        //если нет ошибок и пришла форма добаляем отзыв
        if(!$errors && $is_submit){
            
            $item['user_id'] = $user_id;
            $item['ip']      = $inUser->ip;
            //$item['pubdate'] = date('Y-m-d');
            $item['target'] = 'review';
            
            if($cfg['category_id']==0){
                $item['category_id'] = 0;
            }
            
            //добавляем отзыв
            $review_id = $model->addReview($item);
            //отправляем уведомление админу о новом отзыве
            if($cfg['send_notification'] && !$is_admin) {
                $model->adminNotification($review_id);
            }
            
            if(!$item['published']){
                $msg = $_LANG['ADD_REVIEWS_NOPUB'];
            } else {
                $msg = $_LANG['ADD_REVIEWS_SUCCESS'];
            }
            
            //отправляем уведомление админу о новом отзыве
            if($cfg['send_notification'] && !$is_admin) {
                $model->adminNotification($review_id);
            }
            
            
            $target = array('component' => "spec");
            if(cmsCore::loadModel($target['component'])){
                $model_class = 'cms_model_'.$target['component'];
                if(class_exists($model_class)){
                    $target_model = new $model_class();
                }
            }

            //Пересчитываем количество комментариев у цели если нужно
            if(method_exists($target_model, 'updateReviewsCount')){
                $target_model->updateReviewsCount($review['target'], $review['target_id'], $review_id);
            }
            
            cmsCore::addSessionMessage($msg, 'success');
            //$inCore->redirect('/reviews');
            $inCore->redirectBack();
            

        } else {
            
            $inPage->setTitle($_LANG['ADD_REVIEWS']);
            $inPage->addPathway($_LANG['ADD_REVIEWS']);

            $inPage->addHeadJS('components/reviews/js/reviews.js');
            
            cmsPage::initTemplate('components', 'com_reviews_add')->
                assign('do', $do)->
                assign('catslist', ($cfg['category_id']==-1 ? $inCore->getListItems('cms_reviews_cats', $item['category_id'], 'id', 'ASC', 'published=1') : ''))->
                assign('user_id', $user_id)->
                assign('item', $item)->
                assign('validation', $validation)->
                assign('errors', $errors)->
                assign('cfg', $cfg)->
                display('com_reviews_add.tpl');

        }
    }
//========================================================================================================================//
//========================================================================================================================//
// Добавление отзыва ajax, форма добавления в addform.php
    if ($do=='addajax'){

	if(!cmsCore::isAjax()) { cmsCore::error404(); }
	ob_end_clean();
        
	// Входные данные
	$review['name']        = cmsCore::request('name', 'str', '');
        //$review['phone']       = cmsCore::request('phone', 'str', '');
        //$review['email']       = cmsCore::request('email', 'str', '');
        
        $review['rating']      = cmsCore::request('rate', 'int', 0);
	$review['user_id']     = $inUser->id;
        $review['description'] = cmsCore::request('description', 'str', '');
        $review['description'] = str_replace(array('\r', '\n'), '<br>', $review['description']);
	$review['target']      = cmsCore::request('target', 'str', '');
	$review['target_id']   = cmsCore::request('target_id', 'int', 0);
	$review['ip']          = cmsCore::strClear($_SERVER['REMOTE_ADDR']);
        
        //check rights
	if (!$inUser->is_admin && !$model->can_add_review){
            cmsCore::jsonOutput(array('error' => true, 'text'=>'Нет прав на добавление отзыва'));
        }

	// Проверяем правильность/наличие входных парамеров
	if (!$review['target'] || !$review['target_id']) { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_UNKNOWN_TARGET'])); }
	if (!$review['name'] && !$inUser->id)            { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_USER_NAME'])); }
	if (!$review['description'])                     { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_REVIEW_TEXT'])); }
        if (!$review['rating'])                          { cmsCore::jsonOutput(array('error' => true, 'text' => "Не указана оценка рейтинга")); }
        
        if (!$review['phone'])                           { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_NEED_PHONE'])); }
        
	$need_captcha = $model->config['regcap'] ? true : ($inUser->id ? false : true);
	if ($need_captcha && !cmsCore::checkCaptchaCode(cmsCore::request('code', 'str', ''))) { cmsCore::jsonOutput(array('error' => true, 'is_captcha' => true, 'text' => $_LANG['ERR_CAPTCHA'])); }

        $target = array('component' => "spec");//Жестко прибито
	if (!$target) { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_UNKNOWN_TARGET'] . ' #1')); }

        if(cmsCore::loadModel($target['component'])){
            $model_class = 'cms_model_'.$target['component'];
            if(class_exists($model_class)){
                $target_model = new $model_class();
            }
	}

        if (!isset($target_model)) { cmsCore::jsonOutput(array('error' => true, 'text' => $_LANG['ERR_UNKNOWN_TARGET'] . ' #2')); }

        $review['published']     = ($inUser->is_admin || ($cfg['guest_publish'] && !$user_id) ||  ($cfg['user_publish'] && $user_id)) ? 1 : 0;

        // Проверяем токен перед самым добавлением отзыва
	if(!cmsUser::checkCsrfToken()) { cmsCore::error404(); }

	//добавляем отзыв в базу
	$review_id = $model->addReview($review);
        
        if($review['published']){
            
            //отправляем уведомление админу о новом отзыве
            if($cfg['send_notification'] && !$is_admin) {
                $model->adminNotification($review_id);
            }

            //Пересчитываем количество комментариев у цели если нужно
            if(method_exists($target_model, 'updateReviewsCount')){
                $target_model->updateReviewsCount($review['target'], $review['target_id'], $review_id);
            }
            
        }

	cmsCore::jsonOutput(array('error' => false, 'target' => $review['target'], 'target_id' => $review['target_id'],	'review_id' => $review_id, 'published'=>$review['published'], 'text'=>$_LANG['ADD_REVIEWS_NOPUB']));

    }

/* ==================================================================================================== */
/* ========================== РЕДАКТИРУЕМ ОТЗЫВ ================================================ */
/* ==================================================================================================== */

if ($do == 'edit') {

        //если не админ выгоняем
        if (!$is_admin) {
            AccessDenied();
            return;
        }

        $errors = '';
        $is_submit = $inCore->inRequest('description');
        
        $review = $model->getReview($id);
        
        //если форма пришла:
        if($is_submit){
            
            //данные формы
            $item = array();
            $item['id'] = $id;
            $item['published']     = $inCore->request('published', 'int');
            $item['name']          = $inCore->request('name', 'str');
            $item['link']          = $inCore->request('link', 'str');
            $item['description']   = $inCore->request('description', 'str');
            $item['category_id']   = $inCore->request('category_id', 'int', 0);
            
            //валидация данных
            $validation = array();
            if(!$item['description'] || mb_strlen($item['description'])<10) {$validation['description']=1; $errors .= $_LANG['ERR_DESC'];}
            if(!$item['name']) {$validation['name']=1; $errors .= $_LANG['ERR_NAME'];}
        }
        
        //если нет ошибок и пришла форма редактируем отзыв
        if(!$errors && $is_submit){
            

            //редактируем отзыв
            $model->updateReview($item);
            cmsCore::addSessionMessage($_LANG['EDIT_REVIEWS_SUCCESS'], 'success');
            
            $target = array('component' => "spec");
            if(cmsCore::loadModel($target['component'])){
                $model_class = 'cms_model_'.$target['component'];
                if(class_exists($model_class)){
                    $target_model = new $model_class();
                }
            }

            //Пересчитываем количество комментариев у цели если нужно
            if(method_exists($target_model, 'updateReviewsCount')){
                $target_model->updateReviewsCount($review['target'], $review['target_id'], $item['id']);
            }
            
            
            $inCore->redirect('/reviews');
            
        } else {

            $item = $item ? $item : $model->getReview($id);
            if (!$item['id']) { cmsCore::error404(); }
            
            $inPage->setTitle($_LANG['EDIT_REVIEWS']);
            $inPage->addPathway($_LANG['EDIT_REVIEWS']);

            $inPage->addHeadJS('components/reviews/js/reviews.js');
            
            cmsPage::initTemplate('components', 'com_reviews_add')->
                assign('do', $do)->
                assign('catslist', $inCore->getListItems('cms_reviews_cats', $item['category_id'], 'id', 'ASC', 'published=1'))->
                assign('user_id', $user_id)->
                assign('item', $item)->
                assign('validation', $validation)->
                assign('errors', $errors)->
                assign('cfg', $cfg)->
                display('com_reviews_add.tpl');

        }
    }

/* ==================================================================================================== */
/* ========================== УДАЛЯЕМ ОТЗЫВ ==================================================== */
/* ==================================================================================================== */

    if ($do == 'delete') {

        //если не админ выгоняем
        if (!$is_admin){
            AccessDenied();
            return;
        }

        $review = $inDB->get_fields('cms_reviews', "id='$id'", "id");

        if(!$review['id']) { cmsCore::error404(); }

        $model->deleteReview($id);

        cmsCore::addSessionMessage($_LANG['REVIEW_DELETE'], 'success');
        //$inCore->redirect('/reviews');
        $inCore->redirectBack();

    }
}

function AccessDenied() {
    global $_LANG;
    $inCore = cmsCore::getInstance();
    $smarty = $inCore->initSmarty('components', 'com_error.tpl');
    $smarty->assign('err_title', $_LANG['ACCESS_DENIED']);
    $smarty->assign('err_content', 'Недостаточно прав');
    $smarty->display('com_error.tpl');
    return;
}

function autoLink($text){

    $text = preg_replace('/\s+/', ' ', $text);
    $search = array(
            "/((?:http|https|ftp):\/\/[^<\s]+[^<.,:;?!\"»'\"+\-])([.,:;?!\"»'\"+\-]*(?:<br ?\/?>)*\s|$)/si",
            "/(^|[^\/])(www\.[^<\s]+[^<.,:;?!\"»'\"+\-])([.,:;?!\"»'\"+\-]*(?:<br ?\/?>)*\s|$)/si",
            "'([^\w\d-\.]|^)([\w\d-\.]+@[\w\d-\.]+\.[\w]+[^.,;\s<\"\'\)]+)'si"
        );
    $replace = array(
            '<a href="/go/url=$1" target="_blank">$1</a>$2',
            '$1<a href="/go/url=http://$2" target="_blank">$2</a>$3',
            '$1<a href="mailto:$2">$2</a>'
        );
    $text = preg_replace($search, $replace, $text);

    return $text;
}

?>