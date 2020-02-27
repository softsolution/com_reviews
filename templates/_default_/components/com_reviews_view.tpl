<h1 class="con_heading">{$LANG.REVIEWS}</h1>
<div class=clear></div>

<div id="reviews_body">
{if $is_review}
    {foreach key=tid item=review from=$reviews name=foo}
        <div id="reviews_item" {if $smarty.foreach.foo.index % 2}class="bg_light"{else}class="bg_dark"{/if}>
            <div class="reviews_mess">{$review.description}</div>
            <div class="reviews_author">{$review.name} {if $review.link}<span class="reference-dot">•</span><span class="reviews_contact">{$review.link}{/if}</div>
            {if $cfg.show_date}<div class="reviews_date">{$review.pubdate}</div>{/if}
                
            {if $is_admin}
            {* control panel *}
            <div class="reviews_remote">
                <span class="editlinks">{if !$review.published}<span class="no_public">Не опубликовано</span> | {/if}
                <a title="{$LANG.EDIT}" href="/reviews/edit{$review.id}.html">{$LANG.EDIT}</a>
                | <a title="{$LANG.DELETE}" title="{$LANG.DELETE_REVIEWS}?" onclick="jsmsg('{$LANG.DELETE_REVIEWS}?', '/reviews/delete{$review.id}.html')" href="#">{$LANG.DELETE}</a>
                </span>
            </div>
            {/if}
        </div>
    {/foreach}
    
    {$pagebar}
    <div class=clear></div>
{else}
    <p>{$LANG.NO_REVIEWS}</p>
{/if}
</div>

{if !$user_id && $cfg.guest_enabled || $user_id}
    <div class="reviews_but_wrap">
        <div id="reviewbutton">
            <a href="/reviews/add.html" title="{$LANG.ADD_REVIEWS}" class="button-2"><strong>{$LANG.ADD_REVIEWS}</strong></a>
        </div>
    </div>
{/if}

{if $is_admin}
    {literal}
    <script type="text/JavaScript">
    function jsmsg(msg, link){
        if(confirm(msg)){
            window.location.href = link;
        }
    }
    </script>
    {/literal}
{/if}