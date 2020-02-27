<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component reviews for InstantCMS 1.10.4                                    */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_reviews{
    
    public $can_add_review  = false;
    public $can_moderate    = false;

    function __construct($labels=array()){
        $this->inDB        = cmsDatabase::getInstance();
        $this->config      = cmsCore::getInstance()->loadComponentConfig('reviews');
        cmsCore::loadLanguage('components/reviews');
        $this->labels = $labels ? $labels : self::getDefaultLabels();
    }

    public function __call($name, $arguments){
        exit( "вызван несуществующий метод \"".$name."\"" );
    }
    
    public static function getDefaultConfig() {
    
        $cfg = array(
            'perpage' => '15',
            'amount' => '5',
            'guest_enabled' => '1',
            'user_publish' => '0',
            'guest_publish' => '0',
            'captcha_enabled' => '1',
            'send_notification' => '1',
            'show_date' => '1',
            'category_id' => '0',
            'img_small_w' =>150,
            'img_big_w'=> 400,
            'img_sqr' => 1,
            'watermark' => 0
        );
        
        return $cfg;

    }
    
/* ========================================================================== */
/* ========================================================================== */
    public static function getDefaultLabels() {
        global $_LANG;
        return array('reviews' => $_LANG['REVIEWS'], 'add' => $_LANG['ADD_REVIEWS'], 'not_reviews' => $_LANG['NO_REVIEWS']);
    }

    public function initAccess() {

        $this->can_add_review    = cmsUser::isUserCan('reviews/add');
        $this->can_moderate      = cmsUser::isUserCan('reviews/moderate');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addReview($item) {

        $item['pubdate'] = date('Y-m-d H:i:s');
        $item['description'] = $this->inDB->escape_string($item['description']);
        $review_id = $this->inDB->insert('cms_reviews', $item);

        return $review_id ? $review_id : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getReview($id) {

        $sql = "SELECT * FROM cms_reviews WHERE id = $id LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) {
            return false;
        }

        $review = array();
        $review = $this->inDB->fetch_assoc($result);

        return $review;
    }

/* ========================================================================== */
/* ========================================================================== */
    
    public function getReviews($is_admin=false) {
        
        $items = array();
        
        $published = $is_admin ? '1=1' : 'r.published = 1';
        
        if(!$this->inDB->order_by){
            $this->inDB->orderBy('r.pubdate', 'DESC');
        }
        
        //$this->inDB->where('u.is_locked = 0');
        //$this->inDB->where('u.is_deleted = 0');

        $sql = "SELECT r.*, DATE_FORMAT(r.pubdate, '%d.%m.%Y') as tpubdate, 
                    IFNULL(u.nickname, 0) as nickname, IFNULL(u.id, 0) as author_id, IFNULL(u.login, 0) as login, IFNULL(u.is_deleted, 0) as is_deleted, 
                    IFNULL(p.imageurl, 0) as imageurl 
                {$this->inDB->select}
                FROM cms_reviews r 
                LEFT JOIN cms_users u ON u.id = r.user_id 
                LEFT JOIN cms_user_profiles p ON p.user_id = r.user_id 
                {$this->inDB->join}
                WHERE {$published}
                {$this->inDB->where}
                {$this->inDB->group_by}
                {$this->inDB->order_by}\n";

        if ($this->inDB->limit){
            $sql .= "LIMIT {$this->inDB->limit}";
        }

        $result = $this->inDB->query($sql);

        $this->inDB->resetConditions();

        if (!$this->inDB->num_rows($result)) { return false; }

        while ($item = $this->inDB->fetch_assoc($result)){
            $item['fpubdate']    = cmsCore::dateFormat($item['pubdate'], true, true);
            
            if ($item['name']){
                $item['author']             = $item['guestname'];
                $item['is_profile']         = false;
            } else {
                $item['author']['nickname'] = $item['nickname'];
                $item['author']['login']    = $item['login'];
                $item['is_profile'] 	    = true;
                //$item['user_image'] 	    = cmsUser::getUserAvatarUrl($item['user_id'], 'small', $item['imageurl'], $item['is_deleted']);
            }
            
            $items[] = $item;
        }
        
        return $items;

    }
    
/* ========================================================================== */
/* ========================================================================== */
    /**
     * Получает количество отзывов по заданным параметрам
     * @return int
     */
    
    public function getReviewsTotal($is_admin=false) {
        
        $published = $is_admin ? '1=1' : 'r.published = 1';

        $sql = "SELECT 1 FROM cms_reviews r WHERE {$published}
            {$this->inDB->where}
            {$this->inDB->group_by}
            {$this->inDB->order_by}\n";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

public function updateReview($item) {
    
        $this->inDB->update('cms_reviews', $item, $item['id']);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteReview($id){

        $this->inDB->query("DELETE FROM cms_reviews WHERE id='$id'");

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
/** удаляем группу обзоров
 *
 * @param type $id_list
 * @return boolean
 */
    public function deleteReviews($id_list){
        foreach($id_list as $key=>$id){
            $this->deleteReview($id);
        }
        return true;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */
/** переносим отзыв в общую категорию
 *
 * @param type $id
 * @return boolean
 */
    public function moveToHome($id){

        $this->inDB->query("UPDATE cms_reviews SET category_id = 0 WHERE id = '$id' LIMIT 1");
        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
/** уведомляем админа о новом отзыве
 *
 * @param type $id
 * @return boolean
 */
    public function adminNotification($id){

        //email админа
        $email = $this->inDB->get_field('cms_users', "id='1'", 'email');

        if($email){

            $inCore = cmsCore::getInstance();
            $inConf = cmsConfig::getInstance();

            // Загружаем шаблон письма
            $letter_path = PATH.'/components/reviews/newreview.txt';
            $letter      = file_get_contents($letter_path);
            define('HOST', 'http://' . $inCore->getHost());

            $review_link = '<a href="'.HOST.'/reviews/edit'.$id.'.html" target="_blank">'.HOST.'/reviews/edit'.$id.'.html</a>';
            

            // Заменяем теги в шаблоне на текст
            $letter = str_replace('{sitename}', $inConf->sitename, $letter);
            $letter = str_replace('{review_link}', $review_link, $letter);

            $inCore->mailText($email, 'Новый отзыв на сайте '.$inConf->sitename, $letter);
        }

        return true;
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */
/** добавляем категорию
 *
 * @param type $cat array
 * @return true
 */
    public function addCat($cat){
        
        $sql = "INSERT INTO cms_reviews_cats (title, description, seolink, published)
                        VALUES ('{$cat['title']}', '{$cat['description']}', '{$cat['seolink']}', '{$cat['published']}')";

        $this->inDB->query($sql);
        
       return true; 
    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */
/** редактируем категорию
 *
 * @param type $cat array
 * @return true
 */
    public function updateCat($cat){
                        
        $sql = "UPDATE cms_reviews_cats 
                SET title='{$cat['title']}', 
                        description='{$cat['description']}', 
                        seolink='{$cat['seolink']}', 
                        published='{$cat['published']}' 
                WHERE id = '{$cat['id']}' 
                LIMIT 1";
            
        $this->inDB->query($sql);
        
       return true; 
    }  
    
/* ==================================================================================================== */
/* ==================================================================================================== */
/** удаляем категорию
 *
 * @param type $cat_id
 * @return boolean
 */
    public function deleteCat($cat_id){

        //DELETE CATEGORY
        $sql = "DELETE FROM cms_reviews_cats WHERE id = $cat_id LIMIT 1";
        dbQuery($sql);
        
        $sql = "SELECT id FROM cms_reviews WHERE category_id = '$cat_id'";
        $result = $this->inDB->query($sql);
		
        $item = $this->inDB->fetch_assoc($result);
				
        foreach($item as $key=>$id){
            $this->moveToHome($id);
        }
        
        return true;

    }

}