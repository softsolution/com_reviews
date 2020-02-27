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

    // ������ ���� � ������
    include(PATH.'/core/cms.php');
    // ������ ������
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



    //��������� ������������ ����������
    $cfg_com = $inCore->loadComponentConfig('reviews');

    if(!$cfg_com['guest_enabled'] && !$user_id && !$is_admin){
        echo '<span class="mod_reviews_errors">������ ����� ��������� ������ ������������������ ������������</span>';
        die();
    }

    //���� ����������� ����������� �� ���������� ������� � �����,
    //������� ������� ������� ������������ ������� �������
    if ($cfg_com['amount']!=0 && !$is_admin){
        $user_ip = $inUser->ip;
        $amount_today = $inDB->rows_count('cms_reviews', "DATE(pubdate) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY) AND ip = '$user_ip'");

        if($cfg_com['amount']<=$amount_today){
            echo '<span id="limit" class="mod_reviews_errors">�������� ����� ���������� ������� �� �������. ���������� �����.</a>';
            die();
        }
    }

    $error = '';
    $captha_code           = $inCore->request('code', 'str', '');
    $is_submit             = $inCore->inRequest('description');

    $item['title']         = utf8_to_cp1251($inCore->request('title', 'str', ''));
    $item['contact']       = utf8_to_cp1251($inCore->request('contact', 'str', ''));
    $item['description']   = utf8_to_cp1251($inCore->request('description', 'str', ''));


    if ($captha_code=='' && !$inUser->id && $cfg_com['captcha_enabled']){ $error .= '�� �� ������� ��� � ��������!';}
    if ($is_submit && !$inUser->id && $cfg_com['captcha_enabled'] && !$inCore->checkCaptchaCode($captha_code) && $captha_code!='') { $error .= '����������� ������ ��� � ��������!'; }

    if(!$item['description']) {$error .= '����� �� ����� ���� ������<br/>';}

    if($error){
        // ������ � ������
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
        if(!$item['published']){$pub_moder = '����� �������� ����������� �� ����� �������';}
        $review_id = $model->addReview($item);
        //���������� ����������� ������ � ����� ������
        if($cfg_com['send_notification']) {
            $model->adminNotification($review_id);
        }

        $html = '<span id="addsuccess" class="mod_reviews_success">������� �� ��� �����! <br />'.$pub_moder.'</span>';
        echo $html;

    }
}
?>
