{if $reviews_count}
    {foreach key=tid item=review from=$reviews name=foo}
    <div id="reviews_item" {if $smarty.foreach.foo.index % 2}class="bg_light"{else}class="bg_dark"{/if}>
        <table width="100%" border="0">
            <tr>
                {if $review.is_profile}
                <td width="70" align="center" valign="top" class="review_avatar">
                    <img border="0" class="usr_img_small" src="{$review.user_image}" />
                </td>
                {/if}
                <td class="review_content" valign="top">
                    <div class="rating">
                        {section name=foo start=0 loop=5 step=1}
                            {if $smarty.section.foo.index<$review.rating}<i class="fa fa-star"></i>{else}<i class="fa fa-star-o"></i>{/if}
                        {/section}
                    </div>
                    <h4 class="media-heading">
                        {if !$review.is_profile}
                        <span class="review_author">{$review.author} {if $is_admin && $review.ip}({$review.ip}){/if}</span>
                        {else}
                        <span class="review_author">{$review.author.nickname} {if $is_admin && $review.ip}({$review.ip}){/if}</span>
                        {/if}
                        {if $cfg.show_date}<span class="reviews_date"> <i class="fa fa-calendar"></i> {$review.fpubdate}</span>{/if}
                    </h4>
                    <div class="reviews_mess">{$review.description}</div>
                    {if $is_admin}
                    {* control panel *}
                    <div class="reviews_remote">
                        <span class="editlinks">{if !$review.published}<span class="no_public">Не опубликовано</span> | {/if}
                        <a title="{$LANG.EDIT}" href="/reviews/edit{$review.id}.html">{$LANG.EDIT}</a>
                        | <a title="{$LANG.DELETE}" title="{$LANG.DELETE_REVIEWS}?" onclick="jsmsg('{$LANG.DELETE_REVIEWS}?', '/reviews/delete{$review.id}.html')" href="#">{$LANG.DELETE}</a>
                        </span>
                    </div>
                    {/if}
                </td>
            </tr>
        </table>
    </div>
    {/foreach}
    
    {$pagebar}
    <div class="clearfix"></div>
{/if}