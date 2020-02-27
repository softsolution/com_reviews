<?php
/* ************************************************************************** */
/* created by soft-solution.ru, support@soft-solution.ru                      */
/* component reviews for InstantCMS 1.10.4                                    */
/* license: commercialcc                                                      */
/* Незаконное использование преследуется по закону                            */
/* ************************************************************************** */

    function info_module_mod_reviews(){

        $_module['title']        = 'Последние отзывы';
        $_module['name']         = 'Последние отзывы';
        $_module['description']  = 'Модуль Последние отзывы для компонента Отзывы';
        $_module['link']         = 'mod_reviews';
        $_module['position']     = 'sidebar';
        $_module['author']       = '<a href="http://soft-solution.ru" target="_blank">soft-solution.ru</a>';
        $_module['version']      = '1.3';

        $_module['config'] = array();
	$_module['config']['count'] = 5;
        $_module['config']['category_id'] = -1;
        $_module['config']['view_type'] = 'slider';
        $_module['config']['autoSlide'] = 1;
        $_module['config']['SlideInterval'] = 5000;
        $_module['config']['nav'] = 'top';
        $_module['config']['autoHeight'] = 1;
        $_module['config']['effect'] = 'default';
        $_module['config']['arrow'] = 0;
        $_module['config']['showlink'] = 1;
        $_module['config']['showdate'] = 1;
        $_module['config']['maxlen'] = 100;
        $_module['config']['is_pag'] = 0;
        
        return $_module;

    }

    function install_module_mod_reviews(){

        return true;

    }

    function upgrade_module_mod_reviews(){

        return true;

    }

?>