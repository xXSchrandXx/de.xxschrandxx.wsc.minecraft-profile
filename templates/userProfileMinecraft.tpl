<section class="section">
	{hascontent}
		{content}
			<dl>
				{foreach from=$minecrafts item=minecraft}
					<dt>{$minecraft['title']}</dt>
					<dd>
						<span class="userProfileMinecraft" style="background-image: url('{$minecraft['img']}');" />
					</dd>
				{/foreach}
			</dl>
		{/content}
	{hascontentelse}
		<p class="info" role="status">{lang}wcf.user.profile.content.MinecraftUUIDs.noData{/lang}</p>
	{/hascontent}
</section>