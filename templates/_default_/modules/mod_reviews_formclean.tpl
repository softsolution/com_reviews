{if $error}<div class="mod_reviews_errors">{$error}</div>{/if}
<form action="" method="POST" name="modreviewsform" id="modreviewsform">
    <input name="sid" type="hidden" value="{$sid}">
    <table id="mod_reviews">
        <tr><td align="right"><span class="mod_reviews_title">ФИО</span></td>
            <td>
                <input type="text" maxlength="100" name="title" class="mod_reviews_input" value="{$item.title}" />
            </td>
        </tr>
        <tr height="10"><td colspan="2"></td></tr>
        <tr><td align="right"><span class="mod_reviews_title">Контакт</span></td>
            <td>
                <input type="text" maxlength="100" name="contact" class="mod_reviews_input" value="{$item.contact}" />
            </td>
        </tr>
        <tr height="10"><td colspan="2"></td></tr>
        <tr>
            <td align="right"><span class="mod_reviews_title">Отзыв</span></td>
            <td><textarea name="description"  class="mod_reviews_textarea" id="mod_description">{$item.description}</textarea>
            </td>
        </tr>
    </table>
    {if !$user_id && $cfg_com.captcha_enabled}
        <p style="margin-bottom:10px">
        {php}echo cmsPage::getCaptcha();{/php}
        </p>
    {/if}
    <div class="mod_reviews_control">
        <a id="reviewformsend" href="javascript:void(0)" class="butaction"><span>Оставить отзыв</span></a>
    </div>
</form>