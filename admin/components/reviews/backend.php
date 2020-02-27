<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* backend.php of component reviews for InstantCMS 1.10.3                                     */
/* ****************************************************************************************** */
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

cpAddPathway('Отзывы', '?view=components&do=config&id='.$_REQUEST['id']);
echo '<h3>Отзывы</h3>';
if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list_items'; }
$inCore = cmsCore::getInstance();

$toolmenu = array();

    $toolmenu[0]['icon'] = 'liststuff.gif';
    $toolmenu[0]['title'] = 'Все отзывы';
    $toolmenu[0]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=list_items';

    $toolmenu[1]['icon'] = 'newstuff.gif';
    $toolmenu[1]['title'] = 'Новый отзыв';
    $toolmenu[1]['link'] = '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=add_item';
    
    $toolmenu[2]['icon'] = 'folders.gif';
    $toolmenu[2]['title'] = 'Категории отзывов';
    $toolmenu[2]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats';
    
    $toolmenu[3]['icon'] = 'newfolder.gif';
    $toolmenu[3]['title'] = 'Добавить категорию';
    $toolmenu[3]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=add_cat';

if ($opt != 'config') {
    if ($opt == 'list_items' || $opt == 'show_item' || $opt == 'hide_item') {
        $toolmenu[4]['icon'] = 'edit.gif';
        $toolmenu[4]['title'] = 'Редактировать выбранные';
        $toolmenu[4]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=edit_item&multiple=1');";

        $toolmenu[5]['icon'] = 'show.gif';
        $toolmenu[5]['title'] = 'Публиковать выбранные';
        $toolmenu[5]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=show_item&multiple=1');";

        $toolmenu[6]['icon'] = 'hide.gif';
        $toolmenu[6]['title'] = 'Скрыть выбранные';
        $toolmenu[6]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=hide_item&multiple=1');";

        $toolmenu[7]['icon'] = 'delete.gif';
        $toolmenu[7]['title'] = 'Удалить выбранные';
        $toolmenu[7]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=delete_item&multiple=1');";
    }
    
    $toolmenu[8]['icon'] = 'config.gif';
    $toolmenu[8]['title'] = 'Настройки';
    $toolmenu[8]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=config';
}

if ($opt == 'config') {
    $toolmenu[9]['icon'] = 'save.gif';
    $toolmenu[9]['title'] = 'Сохранить';
    $toolmenu[9]['link'] = 'javascript:document.optform.submit();';
}

cpToolMenu($toolmenu);


//LOAD CURRENT CONFIG
$cfg = $inCore->loadComponentConfig('reviews');
$inCore->loadModel('reviews');


$model = new cms_model_reviews();
$inUser = cmsUser::getInstance();


    $inDB   = cmsDatabase::getInstance();

//CONFIG DEFAULTS
if (!isset($cfg['perpage'])) { $cfg['perpage'] = 15; }
if (!isset($cfg['amount'])) { $cfg['amount'] = 5;}
if (!isset($cfg['guest_enabled'])) { $cfg['guest_enabled'] = 1; }
if (!isset($cfg['guest_publish'])) { $cfg['guest_publish'] = 0; }
if (!isset($cfg['user_publish'])) { $cfg['user_publish'] = 0; }
if (!isset($cfg['captcha_enabled'])) { $cfg['captcha_enabled'] = 1; }
if (!isset($cfg['send_notification'])) { $cfg['send_notification'] = 1; }
if (!isset($cfg['show_date'])) { $cfg['show_date'] = 1; }
if (!isset($cfg['category_id'])) { $cfg['category_id'] = 0; }

//SAVE CONFIG
if($opt=='saveconfig'){
    
    if (!cmsCore::validateForm()) { cmsCore::error404(); }
    
    $cfg = array();
    $cfg['perpage']           = cmsCore::request('perpage', 'int');
    $cfg['amount']            = cmsCore::request('amount', 'int');
    $cfg['guest_enabled']     = cmsCore::request('guest_enabled', 'int');
    $cfg['guest_publish']     = cmsCore::request('guest_publish', 'int');
    $cfg['user_publish']      = cmsCore::request('user_publish', 'int');
    $cfg['captcha_enabled']   = cmsCore::request('captcha_enabled', 'int');
    $cfg['send_notification'] = cmsCore::request('send_notification', 'int');
    $cfg['show_date']         = cmsCore::request('show_date', 'int');
    $cfg['category_id']       = cmsCore::request('category_id', 'int', 0);
    
    $cfg['img_small_w']  = cmsCore::request('img_small_w', 'int', 150);
    $cfg['img_big_w']    = cmsCore::request('img_big_w', 'int', 400);
    $cfg['img_sqr']      = cmsCore::request('img_sqr', 'int');
    $cfg['watermark']    = cmsCore::request('watermark', 'int');
    
    $inCore->saveComponentConfig('reviews', $cfg);
        
    cmsCore::addSessionMessageAdmin('Настройки сохранены', 'success');
    cmsCore::redirectBack();
}

if ($opt=='config') {
    cpAddPathway('Отзывы', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list');
    cpAddPathway('Настройки', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=config');
?>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
    <input type="hidden" name="csrf_token" value="<?php echo cmsUser::getCsrfToken(); ?>" />
    <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
           <tr>
                <td width="">
                    <strong>Количество отзывов:</strong><br/>
                    <span class="hinttext">
                        Количество отзывов на главной странице компонента
                    </span>
                </td>
                <td valign="top">
                    <input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" style="width:50px"/>
                </td>
            </tr>
            <tr>
                <td width="">
                    <strong>Количество отзывов от одного пользователя в сутки:</strong><br/>
                    <span class="hinttext">
                        Оставьте поле пустым для неограниченного количества
                    </span>
                </td>
                <td valign="top">
                    <input name="amount" type="text" id="amount" value="<?php if (@$cfg['amount']>0) echo @$cfg['amount'];?>" style="width:50px"/>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Принимать отзывы от гостей:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="guest_enabled" type="radio" value="1"  <?php if (@$cfg['guest_enabled']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="guest_enabled" type="radio" value="0"  <?php if (@!$cfg['guest_enabled']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Требовать ввод каптчи от гостей:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="captcha_enabled" type="radio" value="1"  <?php if (@$cfg['captcha_enabled']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="captcha_enabled" type="radio" value="0"  <?php if (@!$cfg['captcha_enabled']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Выбор категории / категория по-умолчанию:</strong>
                </td>
                <td>
                    <select name="category_id" id="category_id" style="width:300px">
                        <option value="-1" <?php if ($cfg['category_id']=="-1"){ echo 'selected=""';} ?>>На выбор пользователя</option>
                        <option value="0" <?php if (!$cfg['category_id']){ echo 'selected=""';} ?>>Общая</option>
                        <?php if (isset($cfg['category_id'])){
                                echo $inCore->getListItems('cms_reviews_cats', $cfg['category_id']);
                            } else {
                                echo $inCore->getListItems('cms_reviews_cats');
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Публиковать отзывы пользователей без модерации:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="user_publish" type="radio" value="1"  <?php if (@$cfg['user_publish']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="user_publish" type="radio" value="0"  <?php if (@!$cfg['user_publish']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Публиковать отзывы гостей без модерации:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="guest_publish" type="radio" value="1"  <?php if (@$cfg['guest_publish']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="guest_publish" type="radio" value="0"  <?php if (@!$cfg['guest_publish']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Отправлять уведомление админу о новом отзыве:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="send_notification" type="radio" value="1"  <?php if (@$cfg['send_notification']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="send_notification" type="radio" value="0"  <?php if (@!$cfg['send_notification']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Показывать поле Дата добавления отзыва:</strong><br />
                </td>
                <td valign="top">
                    <label><input name="show_date" type="radio" value="1"  <?php if (@$cfg['show_date']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="show_date" type="radio" value="0"  <?php if (@!$cfg['show_date']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Ширина маленького изображения:</strong><br />
                </td>
                <td><input name="img_small_w" type="text" id="img_small_w" size="5" value="<?php echo @$cfg['img_small_w'];?>"/> px</td>
            </tr>
            <tr>
                <td><strong>Ширина основного изображения:</strong><br />
                <td><input name="img_big_w" type="text" id="img_big_w" size="5" value="<?php echo @$cfg['img_big_w'];?>"/> px</td>
            </tr>
            <tr>
                <td><strong>Квадратные изображения:</strong></td>
                <td>
                    <select name="img_sqr" id="select" style="width:60px">
                        <option value="1" <?php if (@$cfg['img_sqr']=='1') { echo 'selected="selected"'; } ?>>Да</option>
                        <option value="0" <?php if (@$cfg['img_sqr']=='0') { echo 'selected="selected"'; } ?>>Нет</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>Наносить водяной знак:</strong></td>
                <td>
                    <label><input name="watermark" type="radio" value="1" <?php if ($cfg['watermark']) { echo 'checked="checked"'; } ?> /> Да</label>
                    <label><input name="watermark" type="radio" value="0"  <?php if (!$cfg['watermark']) { echo 'checked="checked"'; } ?> /> Нет</label>
                </td>
            </tr>
    </table>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="Сохранить" />
        <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='?view=components&do=config&id=<?php echo (int)$_REQUEST['id']; ?>';"/>
    </p>
</form>

<?php }
/* ==================================================================================================== */
/* ======================== Управление отзывами ======================================================= */
/* ==================================================================================================== */

 if ($opt == 'show_item') {
    if (!isset($_REQUEST['item'])) {
        if (isset($_REQUEST['item_id'])) {
            dbShow('cms_reviews', (int) $_REQUEST['item_id']);
        }
        echo '1';
        exit;
    } else {
        dbShowList('cms_reviews', $_REQUEST['item']);
        $opt = 'list_items';
    }
}

if ($opt == 'hide_item') {
    if (!isset($_REQUEST['item'])) {
        if (isset($_REQUEST['item_id'])) {
            dbHide('cms_reviews', (int) $_REQUEST['item_id']);
        }
        echo '1';
        exit;
    } else {
        dbHideList('cms_reviews', $_REQUEST['item']);
        $opt = 'list_items';
    }
}

/* ==================================================================================================== */
/* ======================== Добавляем и редактируем отзыв ====================================== */
/* ==================================================================================================== */
    
    if ($opt == 'submit_item'){

        $item = array();
        $item['user_id']       = $inUser->id;
        $item['category_id']   = cmsCore::request('category_id', 'int', 0);
        $item['target']        = 'review';
        $item['target_id']     = 0;
        $item['name']          = cmsCore::request('name', 'str');
        $item['position']      = cmsCore::request('position', 'str');
        $item['phone']         = cmsCore::request('phone', 'str');
        $item['email']         = cmsCore::request('email', 'str');
        $item['link']          = cmsCore::request('link', 'str');
        $item['description']   = cmsCore::request('description', 'str');
        $item['rating']        = cmsCore::request('rating', 'int', 0);
        $item['ip']            = $inUser->ip;
        $item['published']     = cmsCore::request('published', 'int');
        
        $pubdate   = cmsCore::request('pubdate', 'str');
        $pubdate   = explode('.', $pubdate);
        $item['pubdate']   = $pubdate[2] . '-' . $pubdate[1] . '-' . $pubdate[0];

        $review_id = $model->addReview($item);
        
        // Загружаем класс загрузки фото
        cmsCore::loadClass('upload_photo');
        $inUploadPhoto = cmsUploadPhoto::getInstance();
        // Выставляем конфигурационные параметры
        $inUploadPhoto->upload_dir    = PATH.'/images/photos/';
        $inUploadPhoto->small_size_w  = $cfg['img_small_w'];
        $inUploadPhoto->medium_size_w = $cfg['img_big_w'];
        $inUploadPhoto->thumbsqr      = $cfg['img_sqr'];
        $inUploadPhoto->is_watermark  = $cfg['watermark'];
        $inUploadPhoto->input_name    = 'picture';
        $inUploadPhoto->filename      = 'review'.$review_id.'.jpg';
        // Процесс загрузки фото
        $inUploadPhoto->uploadPhoto();

        cmsCore::addSessionMessage('Запись успешно добавлена', 'success');
        $inCore->redirect('index.php?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
        
    }

/* ==================================================================================================== */
/* ======================== Редактировать отзыв - сохраняем изменения ================================= */
/* ==================================================================================================== */

    if ($opt == 'update_item'){
        
        $item = array();
        $item['name']          = cmsCore::request('name', 'str');
        $item['position']      = cmsCore::request('position', 'str');
        $item['phone']         = cmsCore::request('phone', 'str');
        $item['email']         = cmsCore::request('email', 'str');
        
        $item['category_id']   = cmsCore::request('category_id', 'int', 0);
        $item['link']          = cmsCore::request('link', 'str');
        $item['description']   = cmsCore::request('description', 'str');
        $item['published']     = cmsCore::request('published', 'int');

        $item['id'] = cmsCore::request('item_id', 'int');
        
        $item['pubdate'] = cmsCore::request('pubdate', 'str');
        $item['olddate'] = cmsCore::request('olddate', 'str');
        $update_date = ($item['pubdate']!=$item['olddate']) ? true : false;
        if($update_date && !strstr($item['pubdate'], '-')){
            $pubdate = explode('.', $item['pubdate']);
            $item['pubdate'] = $pubdate[2] . '-' . $pubdate[1] . '-' . $pubdate[0];
        }

        $model->updateReview($item, $update_date);
        
        $file = 'review'.$item['id'].'.jpg';

        if (cmsCore::request('delete_image', 'int', 0)){
            @unlink(PATH."/images/photos/small/$file");
            @unlink(PATH."/images/photos/medium/$file");
        } else {

            // Загружаем класс загрузки фото
            cmsCore::loadClass('upload_photo');
            $inUploadPhoto = cmsUploadPhoto::getInstance();
            // Выставляем конфигурационные параметры
            $inUploadPhoto->upload_dir    = PATH.'/images/photos/';
            $inUploadPhoto->small_size_w  = $cfg['img_small_w'];
            $inUploadPhoto->medium_size_w = $cfg['img_big_w'];
            $inUploadPhoto->thumbsqr      = $cfg['img_sqr'];
            $inUploadPhoto->is_watermark  = $cfg['watermark'];
            $inUploadPhoto->input_name    = 'picture';
            $inUploadPhoto->filename      = $file;
            // Процесс загрузки фото
            $inUploadPhoto->uploadPhoto();

        }

        if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
            header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
        } else {
            header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item');
        }
    }

/* ==================================================================================================== */
/* ======================== Удалить отзыв/отзывы ====================================================== */
/* ==================================================================================================== */

    if ($opt == 'delete_item') {
        if (!isset($_REQUEST['item'])) {
            if (isset($_REQUEST['item_id'])) {
                $model->deleteReview((int)$_REQUEST['item_id']);
            }
        } else {
            $model->deleteReviews($_REQUEST['item']);
        }
        header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
    }

/* ==================================================================================================== */
/* ======================== Добавить отзыв/Редактировать отзыв ======================================== */
/* ==================================================================================================== */

if ($opt == 'add_item' || $opt == 'edit_item') {
    
    if ($opt == 'add_item') {
        echo '<h3>Добавить отзыв</h3>';
        cpAddPathway('Добавить отзыв', '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=add_item');
        $mod['published'] = 1;
    } else {
        if (isset($_REQUEST['multiple'])) {
            if (isset($_REQUEST['item'])) {
                $_SESSION['editlist'] = $_REQUEST['item'];
            } else {
                echo '<p class="error">Нет выбранных объектов!</p>';
                return;
            }
        }

        $ostatok = '';

        if (isset($_SESSION['editlist'])) {
            $id = array_shift($_SESSION['editlist']);
            if (sizeof($_SESSION['editlist']) == 0) {
                unset($_SESSION['editlist']);
            } else {
                $ostatok = '(На очереди: ' . sizeof($_SESSION['editlist']) . ')';
            }
        } else {
            $id = (int) $_REQUEST['item_id'];
        }

        $sql = "SELECT * FROM cms_reviews WHERE id = $id LIMIT 1";
        $result = $inDB->query($sql);
        if ($inDB->num_rows($result)) {
            $mod = $inDB->fetch_assoc($result);
        }

        echo '<h3>Редактировать отзыв</h3>';
        cpAddPathway('Отзывы', '?view=components&do=config&id=' . (int) $_REQUEST['id'] . '&opt=list_items');
    } ?>

        <form action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
            <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td><strong>Публиковать отзыв?</strong></td>
                    <td><label><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />Да</label>
                    <label><input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />Нет</label></td>
                </tr>
                <tr>
                    <td><strong>Категория отзыва:</strong></td>
                    <td>
                        <select name="category_id" id="category_id" style="width:220px">
                            <option value="0" <?php if (@$mod['category_id']==0) { echo 'selected=""'; } ?>>Общая</option>
                        <?php
                            echo $inCore->getListItems('cms_reviews_cats', $mod['category_id']);
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Дата публикации:</strong></td>
                    <td>
                        <input name="pubdate" type="text" style="width:190px" id="pubdate" <?php if(@!$mod['pubdate']) { echo 'value="'.date('d.m.Y').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?> />
                        <input type="hidden" name="olddate" value="<?php echo @$mod['pubdate'] ?>"/>
                    </td>
                </tr>
                <tr>
                    <td><strong>Имя:</strong></td>
                    <td><input name="name" type="text" size="52" value="<?php echo @$mod['name']; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>Телефон:</strong></td>
                    <td><input name="phone" type="text" size="52" value="<?php echo @$mod['phone']; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><input name="email" type="text" size="52" value="<?php echo @$mod['email']; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>Должность, организация:</strong></td>
                    <td><input name="position" type="text" size="52" value="<?php echo @$mod['position']; ?>" /></td>
                </tr>
                <tr>
                    <td><strong>Ссылка (e-mail или сайт с http://):</strong></td>
                    <td><input name="link" type="text" size="52" value="<?php echo @$mod['link']; ?>" /></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Текст отзыва:</strong></td>
                </tr>
                <tr>
                    <td><strong>Фотография:</strong></td>
                    <td>
                        <?php
                            if ($opt=='edit_item'){
                                if (file_exists(PATH.'/images/photos/small/review'.$mod['id'].'.jpg')){
                        ?>
                        <div style="margin-top:3px;margin-bottom:3px;padding:10px;border:solid 1px gray;text-align:center">
                            <img src="/images/photos/small/review<?php echo $id; ?>.jpg" border="0" />
                        </div>
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="16"><input type="checkbox" id="delete_image" name="delete_image" value="1" /></td>
                                <td><label for="delete_image">Удалить фотографию</label></td>
                            </tr>
                        </table>
                        <?php
                                }
                            }
                        ?>
                        <input type="file" name="picture" style="width:100%" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><textarea name="description"  id="description" rows="10" style="border:solid 1px gray;width:605px"><?php echo @$mod['description']; ?></textarea></td>
                </tr>
            </table>

            <p>
                <label>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($opt == 'add_item') { echo 'value="Добавить отзыв"'; } else { echo 'value="Сохранить изменения"'; } ?> />
                </label>
                <label>
                    <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
                </label>
                <input name="opt" type="hidden" id="do" <?php if ($opt == 'add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
                <?php if ($opt == 'edit_item') { echo '<input name="item_id" type="hidden" value="' . $mod['id'] . '" />'; } ?>
            </p>
        </form>

<?php }

/* ==================================================================================================== */
/* ======================== Список отзывов ============================================================ */
/* ==================================================================================================== */

    if ($opt == 'list_items'){

        cpAddPathway('Отзывы', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');

        //TABLE COLUMNS
        $fields = array();

        $fields[0]['title'] = 'id';		$fields[0]['field'] = 'id';		 $fields[0]['width'] = '30';

        $fields[1]['title'] = 'Имя';	        $fields[1]['field'] = 'name';		 $fields[1]['width'] = '';
        $fields[1]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
        $fields[1]['filter'] = 15;
        $fields[1]['maxlen'] = 80;
        
        $fields[2]['title'] = 'Категория';	$fields[2]['field'] = 'category_id';    $fields[2]['width'] = '200';
        $fields[2]['prc'] = 'cpReviewsCatById'; $fields[2]['filter'] = 1;               $fields[2]['filterlist'] = cpGetList('cms_reviews_cats');

        $fields[3]['title'] = 'Текст отзыва';
        $fields[3]['field'] = 'description';
        $fields[3]['width'] = '';
        
        $fields[4]['title'] = 'Телефон';
        $fields[4]['field'] = 'phone';
        $fields[4]['width'] = '100';

        $fields[5]['title'] = 'ip';
        $fields[5]['field'] = 'ip';
        $fields[5]['width'] = '100';

        $fields[6]['title'] = 'Показ';		$fields[6]['field'] = 'published';	$fields[6]['width'] = '50';
        $fields[6]['do'] = 'opt';               $fields[6]['do_suffix'] = '_item';

        //ACTIONS
        $actions = array();
        $actions[0]['title'] = 'Редактировать';
        $actions[0]['icon']  = 'edit.gif';
        $actions[0]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

        $actions[1]['title'] = 'Удалить';
        $actions[1]['icon']  = 'delete.gif';
        $actions[1]['confirm'] = 'Удалить отзыв?';
        $actions[1]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=delete_item&item_id=%id%';

        //Print table
        cpListTable('cms_reviews', $fields, $actions, '', 'pubdate DESC');
    }

/* ==================================================================================================== */
/* ======================== Показать категорию ======================================================== */
/* ==================================================================================================== */

    if ($opt == 'show_cat'){
        if(isset($_REQUEST['item_id'])) {
            $id = (int)$_REQUEST['item_id'];
            $sql = "UPDATE cms_reviews_cats SET published = 1 WHERE id = $id";
            $inDB->query($sql) ;
            echo '1'; exit;
        }
    }

/* ==================================================================================================== */
/* ======================== Скрыть категорию ========================================================== */
/* ==================================================================================================== */

    if ($opt == 'hide_cat'){
        if(isset($_REQUEST['item_id'])) {
            $id = (int)$_REQUEST['item_id'];
            $sql = "UPDATE cms_reviews_cats SET published = 0 WHERE id = $id";
            $inDB->query($sql) ;
            echo '1'; exit;
        }
    }

/* ==================================================================================================== */
/* ======================== Удаляем категорию  ======================================================== */
/* ==================================================================================================== */

    if($opt == 'delete_cat'){
        if(isset($_REQUEST['item_id'])) {
            $id = (int)$_REQUEST['item_id'];
            $model->deleteCat($id);
            header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
        }
    }
        
/* ==================================================================================================== */
/* ======================== Добавляем категорию отзыва ================================================ */
/* ==================================================================================================== */

    if ($opt == 'submit_cat'){

        $cat = array();
        $cat['title']        = cmsCore::request('title', 'str');
        $cat['published']    = cmsCore::request('published', 'int');
        $cat['description']  = cmsCore::request('description', 'html');
        $cat['description']  = $inDB->escape_string($cat['description']);
        $cat['seolink']      = cmsCore::request('seolink', 'str');
        $cat['seolink']      = str_replace(' ', '', $cat['seolink']);

        if ($cat['seolink']=='') {
            $cat['seolink']  = cmsCore::strToURL($cat['title']);
        } else {
            $cat['seolink']  = cmsCore::strToURL($cat['seolink']);
        }

        $model->addCat($cat);
        
        header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
    }

/* ==================================================================================================== */
/* ======================== Обновляем категорию вопроса =============================================== */
/* ==================================================================================================== */

    if ($opt == 'update_cat'){
        
        if(isset($_REQUEST['item_id'])) {
            
            $cat = array();
            $cat['id']           = cmsCore::request('item_id', 'int');
            $cat['title']        = cmsCore::request('title', 'str');
            $cat['published']    = cmsCore::request('published', 'int');
            $cat['description']  = cmsCore::request('description', 'html');
            $cat['description']  = $inDB->escape_string($cat['description']);
            $cat['seolink']      = cmsCore::request('seolink', 'str');
            $cat['seolink']      = str_replace(' ', '', $cat['seolink']);

            if ($cat['seolink']=='') { $cat['seolink'] = cmsCore::strToURL($cat['title']); } else {
                $cat['seolink'] = cmsCore::strToURL($cat['seolink']);
            }

            $model->updateCat($cat);

            header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');

        }
    }

/* ==================================================================================================== */
/* ======================== Список категорий отзывов ================================================== */
/* ==================================================================================================== */

    if ($opt == 'list_cats'){
        cpAddPathway('Категории отзывов', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
        echo '<h3>Категории отзывов</h3>';

        //TABLE COLUMNS
        $fields = array();

        $fields[0]['title'] = 'id';             $fields[0]['field'] = 'id';		$fields[0]['width'] = '30';

        $fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
        $fields[1]['filter'] = 50;
        $fields[1]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

        $fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';
        $fields[3]['do'] = 'opt'; $fields[3]['do_suffix'] = '_cat';

        //ACTIONS
        $actions = array();
        $actions[0]['title'] = 'Редактировать';
        $actions[0]['icon']  = 'edit.gif';
        $actions[0]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

        $actions[1]['title'] = 'Удалить';
        $actions[1]['icon']  = 'delete.gif';
        $actions[1]['confirm'] = 'Удалить категорию? Все отзывы будут перенесены в общую категорию';
        $actions[1]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';

        //Print table
        cpListTable('cms_reviews_cats', $fields, $actions);
    }
        
/* ==================================================================================================== */
/* ======================== Добавляем и редактируем категорию ========================================= */
/* ==================================================================================================== */

    if ($opt == 'add_cat' || $opt == 'edit_cat'){
        
        if ($opt=='add_cat'){
            echo '<h3>Добавить категорию</h3>';
            cpAddPathway('Добавить категорию', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=add_cat');
            $mod['published'] = 1;
        } else {
            
            if(isset($_REQUEST['item_id'])){
                $id = (int)$_REQUEST['item_id'];
                $sql = "SELECT * FROM cms_reviews_cats WHERE id = $id LIMIT 1";
                $result =$inDB->query($sql) ;
                
                if ($inDB->num_rows($result)){
                    $mod = $inDB->fetch_assoc($result);
                }
                
            }

            echo '<h3>Категория: '.$mod['title'].'</h3>';
            cpAddPathway('Категории вопросов', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
            cpAddPathway($mod['title'], '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id='.(int)$_REQUEST['item_id']);
        
        } ?>

        <form id="addform" name="addform" method="post" enctype="multipart/form-data" action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>">
            <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td><strong>Публиковать категорию?</strong></td>
                    <td>
                        <label><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да </label>
                        <label><input name="published" type="radio" value="0" <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет </label>
                    </td>
                </tr>
                <tr>
                    <td><strong>Название категории: </strong></td>
                    <td width="220"><input name="title" type="text" id="title" style="width:220px" value="<?php echo htmlspecialchars($mod['title']); ?>"/></td>
                </tr>
                <tr>
                    <td><strong>URL категории: </strong><br />
                    <span class="hinttext">Только латинские символы. Оставьте пустым для автоматической генерации</span>
                    </td>
                    <td width="220"><input name="seolink" type="text" id="seolink" style="width:220px" value="<?php echo @$mod['seolink'];?>"/></td>
                </tr>
               </table>
            <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr><td><strong>Описание категории:</strong><br />
                        <span class="hinttext">Будет показываться при просмотре категории</span>
                    </td>
                </tr>
                <tr>
                    <td>
                    <?php $inCore->insertEditor('description', $mod['description'], '260', '600'); ?>
                    </td>
                </tr>
            </table>
            <p>
                <label>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add_cat') { echo 'value="Создать категорию"'; } else { echo 'value="Сохранить изменения"'; } ?> />
                </label>
                <label>
                    <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id']; ?>';"/>
                </label>
                    <input name="opt" type="hidden" id="do" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
                    <?php
                    if ($opt=='edit_cat'){
                        echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                    }
                    ?>
            </p>
        </form>
    <?php 
    }

?>

 <?php

 function cpReviewsCatById($id){
    $inDB   = cmsDatabase::getInstance();
    $result = $inDB->query("SELECT title FROM cms_reviews_cats WHERE id = $id") ;

    if ($inDB->num_rows($result)) {
        $cat = $inDB->fetch_assoc($result);
        return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a>';
    } else {
        return 'Общая';
    }

}

 ?>