Установка - как стандартный компонент

подключение к компоненту

if($inCore->isComponentInstalled('reviews')){
    include_once PATH."/components/reviews/frontend.php";
    reviews('mapitem', $item['id'], array(
        'reviews' => $_LANG['MAPS_REVIEWS'],
        'add' => $_LANG['MAPS_REVIEWS_ADD'],
        'not_reviews' => $_LANG['MAPS_REVIEWS_NO']
    ));
}

Языковые переменные
$_LANG['MAPS_REVIEWS']            = 'Отзывы';
$_LANG['MAPS_REVIEWS_ADD']        = 'Добавить отзыв';
$_LANG['MAPS_REVIEWS_RSS']        = 'Лента отзывов';
$_LANG['MAPS_REVIEWS_NO']         = 'Нет отзывов. Ваш будет первым!';