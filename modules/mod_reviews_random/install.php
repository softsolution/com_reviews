<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    function info_module_mod_reviews_random(){

        $_module['title']        = 'Случайный отзыв';
        $_module['name']         = 'Случайный отзыв';
        $_module['description']  = 'Модуль Случайный отзыв для компонента Отзывы';
        $_module['link']         = 'mod_reviews_random';
        $_module['position']     = 'sidebar';
        $_module['author']       = 'soft-solution.ru';
        $_module['version']      = '1.0';
        $_module['config'] = array();
		$_module['config']['reviewscount'] = 1;
        $_module['config']['maxlen'] = 0;
        $_module['config']['showlink'] = 1;

        return $_module;

    }
	
    function install_module_mod_reviews_random(){

        return true;

    }
	
    function upgrade_module_mod_reviews_random(){

        return true;

    }
?>