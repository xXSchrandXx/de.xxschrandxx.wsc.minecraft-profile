<ul class="sidebarItemList">
	<li>
	<div class="sidebarItemTitle">
		<h3>Default</h3>
			<img src="images/skins/default-FACE.png" />
		</div>
	</li>
	{foreach from=$boxMinecraftProfileList item=boxMinecraftProfile}
		<li>
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