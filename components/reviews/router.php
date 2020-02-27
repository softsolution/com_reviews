<?php
/* ****************************************************************************************** */
/* created by soft-solution.ru                                                                */
/* router.php of component reviews for InstantCMS 1.9                                         */
/* ****************************************************************************************** */
function routes_reviews() {

    //добавление отзыва
    $routes[] = array(
        '_uri' => '/^reviews\/add.html$/i',
        'do' => 'add'
    );

    //ajax добавление отзыва
    $routes[] = array(
        '_uri' => '/^reviews\/add$/i',
        'do' => 'addajax'
    );

    //редактирование отзыва
    $routes[] = array(
        '_uri' => '/^reviews\/edit([0-9]+).html$/i',
        'do' => 'edit',
        1 => 'id'
    );

    //удаление отзыва
    $routes[] = array(
        '_uri' => '/^reviews\/delete([0-9]+).html$/i',
        'do' => 'delete',
        1 => 'id'
    );

    //все отзывы (пагинация)
    $routes[] = array(
        '_uri' => '/^reviews\/page-([0-9]+)$/i',
        'do' => 'view',
        'target' => 'all',
        1 => 'page'
    );

    //все отзывы
    $routes[] = array(
        '_uri' => '/^reviews\/$/i',
        'do' => 'view',
        'target' => 'all'
    );

    return $routes;
}

?>