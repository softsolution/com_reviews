<div class="review_addentry">
{if $user_can_add}
    <form action="/reviews/{$do}" method="POST" id="reviewform">
        <input type="hidden" name="review_id" value="{$review.id}" />
        <input type="hidden" name="csrf_token" value="{csrf_token}" />
        <input type="hidden" name="target" value="{$target}"/>
        <input type="hidden" name="target_id" value="{$target_id}"/>
        
        <div class="item_rating">
            {section name=rate start=1 loop=6 step=1}
                <input name="rate" type="radio" class="star required" title="{$ratings_text[$smarty.section.rate.index]}" value="{$smarty.section.rate.index}" {if $review.rating>=$smarty.section.rate.index}checked="checked"{/if} />
            {/section}
            <span id="hover-text" style="margin:0 0 0 20px;">Оцените по шкале</span>
        </div>
        <div class="clearfix"></div>
        <div class="review_editor">
            <textarea id="description" name="description" class="ajax_autogrowarea" style="height:150px;min-height: 150px;word-wrap: break-word;" placeholder="Введите тект отзыва">{$review.description|escape:'html'}</textarea>
        </div>
        <div class="submit_review" style="margin:10px 0 0 0;">
        {if $do=='add'}
            <input id="submit_review" type="button" value="{$LANG.SEND}" class="btn btn-normal" />
            <a id="cancel_review" onclick="$('.review_addentry').remove();$('.review_add_link').show();" class="btn btn-default">{$LANG.CANCEL}</a>
        {/if}
        </div>
    </form>
    <div class="sess_messages" {if !$notice}style="display:none"{/if}>
        <div class="message_info" id="error_mess">{$notice}</div>
    </div>
{else}
    <p>У вас нет прав на добавление отзывов</p>
{/if}
</div>