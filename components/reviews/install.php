<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* install.php of component reviews for InstantCMS 1.9                                        */
/* ****************************************************************************************** */
function info_component_reviews() {
    $_component['title'] = 'Отзывы';                                          //название
    $_component['description'] = 'Компонент Отзывы позволяет пользователям оставлять свое мнение и оценку о пользователе, в целом о сайте и его услугах.';                          //описание
    $_component['link'] = 'reviews';                                          //ссылка (идентификатор)
    $_component['author'] = 'soft-solution.ru';                               //автор
    $_component['internal'] = '0';                                            //внутренний (только для админки)? 1-Да, 0-Нет
    $_component['version'] = '1.1';                                           //текущая версия

    //Настройки по-умолчанию
    $_component['config'] = array(
        'perpage' => '15',
        'amount' => '5',
        'guest_enabled' => '1',
        'user_publish' => '0',
        'guest_publish' => '0',
        'captcha_enabled' => '1',
        'send_notification' => '1',
        'show_date' => '1',
        'category_id' => '0'
    );

    return $_component;
}

function install_component_reviews() {

    $inCore = cmsCore::getInstance();                                //подключаем ядро
    $inDB = cmsDatabase::getInstance();                              //подключаем базу данных
    $inConf = cmsConfig::getInstance();

    include($_SERVER['DOCUMENT_ROOT'] . '/includes/dbimport.inc.php');

    dbRunSQL($_SERVER['DOCUMENT_ROOT'] . '/components/reviews/install.sql', $inConf->db_prefix);

    return true;
}

function upgrade_component_reviews() {

    return true;

}

?>