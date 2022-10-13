{hascontent}
	<ul class="sidebarItemList" style="display: flex; gap: 10px;">
		{content}
			{foreach from=$boxMinecraftOnlineList key=boxMinecraftOnlineUUID item=boxMinecraftOnline}
				<li>
					{if $boxMinecraftOnline['user']|isset}
						<a href="{$boxMinecraftOnline['user']->getLink()}" data-object-id="{$boxMinecraftOnline['user']->userID}" class="userLink">
					{/if}
					{if $boxMinecraftOnline['hasGeneratedImage']}
						<img src="/images/skins/{$boxMinecraftOnlineUUID}-{$boxMinecraftOnlineImageType}.png" title="{$boxMinecraftOnline['minecraftName']}" alt="{$boxMinecraftOnline['minecraftName']}" width="{$boxMinecraftOnlineImageWidth}"/>
					{else}
						<img src="/images/skins/default-{$boxMinecraftOnlineImageType}.png" title="{$boxMinecraftOnline['minecraftName']}" alt="{$boxMinecraftOnline['minecraftName']}" width="{$boxMinecraftOnlineImageWidth}"/>
					{/if}
					{if $boxMinecraftOnline['user']|isset}
						</a>
					{/if}
				</li>
			{/foreach}
		{/content}
	</ul>
{hascontentelse}
	<p>{lang}wcf.global.noItems{/lang}</p>
{/hascontent}