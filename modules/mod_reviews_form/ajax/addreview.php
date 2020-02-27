<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die();}
    header('Content-Type: text/html; charset=windows-1251');
    session_start();

    //PROTECT FROM DIRECT RUN
    if (isset($_REQUEST['sid'])){
        if (md5(session_id()) != $_REQUEST['sid']){ die(); }
    } else {
        die();
    }

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    // Грузим ядро и классы
    include(PATH.'/core/cms.php');
    // Грузим конфиг
    include(PATH.'/includes/config.inc.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('user');
    $inCore->loadClass('page');
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $inUser->update();
    $user_id   = $inUser->id;
    $is_admin  = $inCore->userIsAdmin($user_id);



    //Загружаем конфигурацию компонента
    $cfg_com = $inCore->loadComponentConfig('reviews');

    if(!$cfg_com['guest_enabled'] && !$user_id && !$is_admin){
        echo '<span class="mod_reviews_errors">Отзывы могут добавлять только зарегистрированные пользователи</span>';
        die();
    }

    //если установлено ограничение на количество отзывов в сутки,
    //считаем сколько отзывов пользователь добавил сегодня
    if ($cfg_com['amount']!=0 && !$is_admin){
        $user_ip = $inUser->ip;
        $amount_today = $inDB->rows_count('cms_reviews', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

        if($cfg_com['amount']<=$amount_today){
            echo '<span id="limit" class="mod_reviews_errors">Исчерпан лимит добавления отзывов на сегодня. Попробуйте позже.</a>';
            die();
        }
    }

    $error = '';
    $captha_code           = $inCore->request('code', 'str', '');
    $is_submit             = $inCore->inRequest('description');

    $item['title']         = utf8_to_cp1251($inCore->request('title', 'str', ''));
    $item['contact']       = utf8_to_cp1251($inCore->request('contact', 'str', ''));
    $item['description']   = utf8_to_cp1251($inCore->request('description', 'str', ''));


    if ($captha_code=='' && !$inUser->id && $cfg_com['captcha_enabled']){ $error .= 'Вы не указали код с картинки!';}
    if ($is_submit && !$inUser->id && $cfg_com['captcha_enabled'] && !$inCore->checkCaptchaCode($captha_code) && $captha_code!='') { $error .= 'Неправильно указан код с картинки!'; }

    if(!$item['description']) {$error .= 'Отзыв не может быть пустым<br/>';}

    if($error){
        // Отдаем в шаблон
        ob_start();
        $smarty = $inCore->initSmarty('modules', 'mod_reviews_formclean.tpl');
        $smarty->assign('error', $error);
        $smarty->assign('item', $item);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('cfg_com', $cfg_com);
        $smarty->assign('sid', md5(session_id()));
        $smarty->display('mod_reviews_formclean.tpl');
        $html = ob_get_clean();
        echo $html;

    } else {

        $inCore->loadModel('reviews');
        $model = new cms_model_reviews();


        $item['user_id']   = $user_id;
        $item['ip']        = $inUser->ip;
        $item['published'] = ($inUser->is_admin || $cfg_com['guest_publish']) ? 1 : 0;
        if(!$item['published']){$pub_moder = 'После проверки модератором он будет показан';}
        $review_id = $model->addReview($item);
        //отправляем уведомление админу о новом отзыве
        if($cfg_com['send_notification']) {
            $model->adminNotification($review_id);
        }

        $html = '<span id="addsuccess" class="mod_reviews_success">Спасибо за ваш отзыв! <br />'.$pub_moder.'</span>';
        echo $html;

    }
}
?>
