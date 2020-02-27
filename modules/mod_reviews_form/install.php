<?php
/*==================================================*/
/*            created by soft-solution.ru           */
/*==================================================*/

    function info_module_mod_reviews_form(){

        $_module['title']        = 'Добавить отзыв';
        $_module['name']         = 'Добавить отзыв';
        $_module['description']  = 'Модуль Форма добавления отзыва для компонента Отзывы';
        $_module['link']         = 'mod_reviews_form';
        $_module['position']     = 'sidebar';
        $_module['author']       = 'soft-solution.ru';
        $_module['version']      = '1.0';

        return $_module;

    }

    function install_module_mod_reviews_form(){

        return true;

    }

    function upgrade_module_mod_reviews_form(){

        return true;

    }

?>