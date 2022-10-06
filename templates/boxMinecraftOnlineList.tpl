{hascontent}
	<ul class="sidebarItemList">
		{content}
			{foreach from=$boxMinecraftProfileList item=boxMinecraftProfile}
				<li>
					<div class="sidebarItemTitle">
						{if $boxMinecraftProfile->hasGeneratedImage()}
							<img src="images/skins/{$boxMinecraftProfile->getMinecraftUUID()}-FACE.png" alt="{$boxMinecraftProfile->getMinecraftName()}"/>
						{else}
							<img src="images/skins/default-FACE.png" alt="{$boxMinecraftProfile->getMinecraftName()}"/>
						{/if}
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{hascontentelse}
	<p>{lang}wcf.global.noItems{/lang}</p>
{/hascontent}