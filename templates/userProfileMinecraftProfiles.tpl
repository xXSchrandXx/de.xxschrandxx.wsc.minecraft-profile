{if !$minecraftProfilesByMinecraft|empty}
	{foreach from=$minecraftProfilesByMinecraft key=minecraftID item=minecraftProfiles}
		{hascontent}
			<section class="section">
				<h2 class="sectionTitle">{$minecrafts[$minecraftID]->getTitle()}</h2>
				<ul class="inlineList" style="gap: 10px;">
					{content}
						{foreach from=$minecraftProfiles key=minecraftProfileID item=minecraftProfile}
							<li>
								{event name='beforeMinecraftProfile'}
								<span class="badge{if $minecraftProfile->isOnline()} green{/if}" style="display: block; text-align: center;">
									{$minecraftProfile->getMinecraftName()}
								</span>
								<img 
									src="{$__wcf->getPath()}images/skins/{if $minecraftProfile->hasGeneratedImage()}{$minecraftProfile->getMinecraftUUID()}{else}default{/if}-FRONT.png" 
									title="{$minecraftProfile->getMinecraftName()}" 
									alt="{$minecraftProfile->getMinecraftName()}"
									style="display: block;"
								/>
								{event name='afterMinecraftProfile'}
							</li>
						{/foreach}
					{/content}
				</ul>
			</section>
		{hascontentelse}
			<div class="section">
				<woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
			</div>
		{/hascontent}
	{/foreach}
{else}
	<div class="section">
		<woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
	</div>
{/if}
