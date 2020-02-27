<link href="/templates/{$template}/css/reviews.css" rel="stylesheet" type="text/css" />

{if $is_reviews}
    <div id="mod_reviews_body">
        {if $cfg.view_type == "list"}
            {foreach key=aid item=review from=$reviews}
                <div class="mod_review_entry">
                    {if $cfg.showdate}
                        <div class="mod_review_date">{$review.pubdate}</div>
                    {/if}
                    <div class="mod_review_mess">{$review.description}</div>
                    <div class="mod_review_author">{$review.name}</div>
                </div>
            {/foreach}
        {elseif $cfg.view_type == "slider"}
            {* slider *}
            <link rel="stylesheet" href="/modules/mod_reviews/css/coda-slider-2.0.css" type="text/css" media="screen" />
            <script type="text/javascript" src="/modules/mod_reviews/js/jquery.easing.1.3.js"></script>
            <script type="text/javascript" src="/modules/mod_reviews/js/jquery.coda-slider-2.0.js"></script>
            <div class="coda-slider-wrapper">
                {if $cfg.arrow}
                <div id="coda-nav-left-1" class="coda-nav-left"><a href="#" title="Назад">&#171;</a></div>
                <div id="coda-nav-right-1" class="coda-nav-right"><a href="#" title="Вперед">&#187;</a></div>
                {/if}
                <div class="coda-slider preload" id="coda-slider{$mid}">
                {foreach key=aid item=review from=$reviews}
                    <div class="panel">
                        <div class="panel-wrapper">
                            <div class="tab-nav"></div>
                            {if $cfg.showdate}
                                <div class="mod_review_date">{$review.pubdate}</div>
                            {/if}
                            <div class="mod_review_mess">{$review.description}</div>
                            <div class="mod_review_author">{$review.name}</div>
                        </div>
                    </div>
                {/foreach}
                </div>
            </div>
            
            {literal}
            <script type="text/javascript">
            $().ready(function() {
                $('#coda-slider{/literal}{$mid}{literal}').codaSlider({
                    {/literal}
                    {if $cfg.autoSlide}
                    autoSlide: true,
                    autoSlideInterval: {$cfg.SlideInterval},
                    {/if}
                    {if $cfg.effect!='default'}
                    slideEaseFunction: '{$cfg.effect}',
                    {/if}
                    {if !$cfg.autoHeight || $cfg.autoHeight==0}
                    autoHeight: false,
                    {/if}
                    {if $cfg.nav=='hide'}
                    dynamicTabs: false,
                    {else}
                    dynamicTabsPosition: '{$cfg.nav}',
                    {/if}
                    {literal}
                    autoSlideStopWhenClicked: true,
                    dynamicArrows: false,
                    {/literal}
                    panelTitleSelector: 'div.tab-nav'
                    {literal}
                });
            });
            </script>
            {/literal}
        {else}
            <link rel="stylesheet" type="text/css" href="/modules/mod_reviews/css/amazon_scroller.css" />
            <script type="text/javascript" src="/modules/mod_reviews/js/amazon_scroller.js"></script>
            
            <div id="scroller{$mid}" class="amazon_scroller">
                <div class="amazon_scroller_mask">
                    <ul>
                        {foreach key=aid item=review from=$reviews}
                        <li>
                            <div class="mod_reviews_entry">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="mod_reviews_image">
                                                <img src="/images/users/avatars/small/9e39f38f16e86b2e031cc3aa4b3be6c9.png" width="64" border="0" />
                                            </div>
                                       </td>
                                       <td>
                                            {if $cfg.showdate}
                                                <div class="mod_review_date">{$review.pubdate}</div>
                                            {/if}
                                            <div class="mod_review_mess">{$review.description}</div>
                                            <div class="mod_review_author">{$review.name}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </li>
                        {/foreach}
                    </ul>
                </div>
                {if $cfg.arrow}
                <div id="scroller-nav-left" class="scroller-nav-left"><a href="javascript:void(0)" title="Назад">&#171;</a></div>
                <div id="scroller-nav-right" class="scroller-nav-right"><a href="javascript:void(0)" title="Вперед">&#187;</a></div>
                {/if}
                <div style="clear: both"></div>
            </div>
            
                    
{* amazon scroller init params *}
{literal}
    <script language="javascript" type="text/javascript">
        $(function() {
            
            var width = {/literal}{$cfg.scroller_slide_width}{literal};
            var height = {/literal}{$cfg.scroller_slide_height}{literal};
            
            $("#scroller{/literal}{$mid}{literal}").amazon_scroller({
                scroller_title_show: 'disable',
                scroller_time_interval: '{/literal}{$cfg.SlideInterval}{literal}',
                scroller_window_background_color: "none",
                scroller_window_padding: '10',
                scroller_border_size: '0',
                scroller_border_color: '#CCC',
                scroller_images_width: width,
                scroller_images_height: height,
                scroller_title_size: '12',
                scroller_title_color: 'black',
                scroller_show_count: '{/literal}{$cfg.scroller_show_count}{literal}',
                directory: 'images'
            });

        });
    </script>
{/literal}

{* !внимание чтобы стили пересеклись добавлена привязка к родительскому блоку *}
{literal}
<style>
#scroller{/literal}{$mid}{literal} .mod_reviews_entry{width:{/literal}{$cfg.scroller_slide_width}px;height:{$cfg.scroller_slide_height}{literal}px; display:block;}
</style>
{/literal}   
           
        {/if}
    </div>
    {if $cfg.is_pag && $pagebar_module}
        <div class="mod_reviews_pagebar">{$pagebar_module}</div>
    {/if}

    {if $cfg.showlink}
        <a class="mod_reviews_all button top-4" href="/reviews"><strong>Все отзывы</strong></a>
    {/if}

{else}
    <p>Нет отзывов</p>
{/if}