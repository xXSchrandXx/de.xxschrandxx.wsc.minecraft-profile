{hascontent}
	<ul class="sidebarItemList" style="display: flex; gap: 10px;">
		{content}
			{foreach from=$boxMinecraftOnlineList key=boxMinecraftOnlineUUID item=boxMinecraftOnline}
				<li>
					{if $boxMinecraftOnline['user']|isset}
						<a href="{$boxMinecraftOnline['user']->getLink()}" data-object-id="{$boxMinecraftOnline['user']->userID}" class="userLink">
					{/if}
					{if $boxMinecraftOnline['hasGeneratedImage']}
						<img src="images/skins/{$boxMinecraftOnlineUUID}-FACE.png" title="{$boxMinecraftOnline['minecraftName']}" alt="{$boxMinecraftOnline['minecraftName']}" with="32" height="32"/>
					{else}
						<img src="images/skins/default-FACE.png" title="{$boxMinecraftOnline['minecraftName']}" alt="{$boxMinecraftOnline['minecraftName']}" with="32" height="32"/>
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