<link href="/templates/{template}/css/reviews.css" rel="stylesheet" type="text/css" />
<div id="mod_reviews_random">
    <a href="javascript:reviewsReload({$module_id})" title="Обновить" id="mod_reviews_random_reload"></a>

    <div class="preloader" style="display:none;">loading</div>
{if $is_reviews}
<div id="mod_reviews_random_entry">

    {include file='mod_reviews_random_entry.tpl'}

</div>

{if $cfg.showlink}
<div class="allreviews_block">
    <a class="allreviews" href="/reviews">Прочитать все отзывы</a>
</div>
{/if}

{else}
    <p>Нет отзывов</p>
{/if}
</div>
{literal}
<script type="text/javascript">
function reviewsReload(module_id){
    $('#mod_reviews_random .preloader').show();
    $.post('/modules/mod_reviews_random/ajax/getreview.php', {'module_id': module_id}, function(data){
        $('#mod_reviews_random_entry').html(data);
    });
    $('#mod_reviews_random .preloader').hide();
}
</script>
{/literal}