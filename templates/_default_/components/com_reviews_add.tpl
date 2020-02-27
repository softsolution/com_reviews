{if $do=='edit'}
<div class="wrap_review_button">
    <div id="reviewbutton">
        <a href="/reviews/delete{$item.id}.html" title="{$LANG.DELETE_REVIEWS}" class="reviewbut">{$LANG.DELETE_REVIEWS}</a>
    </div>
</div>
{/if}

<h1 class="con_heading">{if $do=='add'}{$LANG.ADD_REVIEWS}{else}{$LANG.EDIT_REVIEWS}{/if}</h1>
<div class=clear></div>

<div id="reviews_body">
{if $errors}
    <p style="color:red">{$errors}</p>
{/if}
    <form action="" method="POST" name="reviews_addform" id="reviews_addform">
        <table id="add_table_review">
            {if $do=='edit'}
            <tr>
                <td>
                    <span class="name_field_review">{$LANG.PUB_REVIEWS}?</span>
                    <label><input type="radio" {if $item.published}checked="checked"{/if} value="1" name="published">{$LANG.YES}</label>
                    <label><input type="radio" {if !$item.published}checked="checked"{/if} value="0" name="published">{$LANG.NO}</label>
                </td>
            </tr>
            {/if}
            
            {if $do=='edit' || $cfg.category_id=="-1"}
            <tr>
                <td>{$LANG.CAT_REVIEWS}</td>
            </tr>
            <tr>
                <td>
                    <select name="category_id" class="reviews_select">
                        <option value="0" {if $item.category_id=="0"}selected=""{/if}>{$LANG.CAT_GENERAL}</option>
                        {$catslist}
                    </select>
                </td>
            </tr>
            {/if}

            <tr>
                <td>
                    <span class="name_field_review">{$LANG.TITLE_REVIEWS}<span class="callstar">*</span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="name" class="review_field fullfield" type="text" size="52" id="name" value="{$item.name}" />
                    <div id="titlecheck">{if $validation.name}<p class="novalid">{$LANG.DONT_EMPTY}</p>{/if}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="name_field_review">{$LANG.LINK}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <input name="link" class="review_field fullfield" type="text" size="52" id="link" value="{$item.link}" />
                </td>
            </tr>
            <tr>
                <td>
                    <span class="name_field_review">{$LANG.DESC_REVIEWS}<span class="callstar">*</span></span>
                </td>
            </tr>
            <tr>
                <td>
                    <textarea name="description"  class="review_field fullfield" id="review_description" rows="10" cols="40">{$item.description}</textarea>
                    <div id="description_check">{if $validation.description}<p class="novalid">{$LANG.ERR_DESC}</p>{/if}</div>
                </td>
            </tr>
        </table>

        {if !$user_id && $cfg.captcha_enabled}
        <p style="margin-bottom:10px">
            {php}echo cmsPage::getCaptcha();{/php}
        </p>
        {/if}

        <div class="control">
            <input type="button" onclick="sendReview()" name="gosend" value="{if $do=='edit'}{$LANG.SAVE}{else}{$LANG.ADD}{/if}"/>
            <input type="button" name="cancel" onclick="window.history.go(-1)" value="{$LANG.CANCEL}"/>
        </div>
    </form>
</div>