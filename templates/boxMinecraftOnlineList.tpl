{hascontent}
	<ul class="sidebarItemList" style="display: flex; gap: 10px;">
		{content}
			{foreach from=$boxMinecraftProfiles item=boxMinecraftProfile}
				<li>
					{if !$boxMinecraftUsers|empty && $boxMinecraftUsers[$boxMinecraftProfile->getMinecraftUUID()]|isset}
						<a href="{$boxMinecraftUsers[$boxMinecraftProfile->getMinecraftUUID()]->getLink()}" data-object-id="{$boxMinecraftUsers[$boxMinecraftProfile->getMinecraftUUID()]->userID}" class="userLink">
					{/if}
					{if $boxMinecraftProfile->hasGeneratedImage()}
						<img src="images/skins/{$boxMinecraftProfile->getMinecraftUUID()}-FACE.png" title="{$boxMinecraftProfile->getMinecraftName()}" alt="{$boxMinecraftProfile->getMinecraftName()}" with="32" height="32"/>
					{else}
						<img src="images/skins/default-FACE.png" title="{$boxMinecraftProfile->getMinecraftName()}" alt="{$boxMinecraftProfile->getMinecraftName()}" with="32" height="32"/>
					{/if}
					{if !$boxMinecraftUsers|empty && $boxMinecraftUsers[$boxMinecraftProfile->getMinecraftUUID()]|isset}
						</a>
					{/if}
				</li>
			{/foreach}
		{/content}
	</ul>
{hascontentelse}
	<p>{lang}wcf.global.noItems{/lang}</p>
{/hascontent}