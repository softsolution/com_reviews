<table width="100%" cellspacing="1" cellpadding="2" border="0">
	<tbody>
		{foreach key=aid item=review from=$reviews}
			<tr>
				<td valign=top>
					<div class="author">
						{$review.title}
					</div>
					<div class="contact">
						{$review.contact}
					</div>
						{*<div class="mod_review_date">{$review.pubdate}</div>*}
				</td>
			</tr>
			<tr>
				<td valign=top>
					<div class="user_arrow"></div>
					<div class="entry">
						<div class="desc">{$review.description}</div>
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>