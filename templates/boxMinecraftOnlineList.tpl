<ul class="sidebarItemList">
	{foreach from=$boxMinecraftProfileList item=boxMinecraftProfile}
		<li class="box24">
			<div class="sidebarItemTitle">
				<h3>{$boxMinecraftProfile->getMinecraftName()}</h3>
				{if $boxMinecraftProfile->hasGeneratedImage()}
					<img src="images/skins/{$boxMinecraftProfile->getMinecraftUUID()}-FACE.png" />
				{else}
					<img src="images/skins/default-FACE.png" />
				{/if}
			</div>
		</li>
	{/foreach}
</ul>