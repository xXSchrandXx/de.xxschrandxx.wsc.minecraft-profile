<section class="section">
	{hascontent}
		{content}
			<dl>
				{foreach from=$minecrafts item=minecraft}
					<dt>{$minecraft['name']}</dt>
					<dd>
						<img src="{$__wcf->getPath()}{$minecraft['img']}" alt="{$minecraft['name']}"/>
					</dd>
				{/foreach}
			</dl>
		{/content}
	{hascontentelse}
		<p class="info" role="status">{lang}wcf.user.profile.content.MinecraftUUIDs.noData{/lang}</p>
	{/hascontent}
</section>