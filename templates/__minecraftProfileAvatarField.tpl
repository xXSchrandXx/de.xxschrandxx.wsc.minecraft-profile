{if MINECRAFT_PROFILE_IDENTITY !== null && $__wcf->getSession()->getPermission('user.profile.avatar.canUseMinecraftProfile')}
	{if $minecraftProfiles|isset && !$minecraftProfiles|empty}
		{foreach from=$minecraftProfiles item=minecraftProfile}
			<dl class="avatarType{if $errorField == 'minecraftProfile'|concat:$minecraftProfile->getObjectID()} formError{/if}">
				<dt>
					<img src="{$__wcf->getPath()}images/skins/{if $minecraftProfile->hasGeneratedImage()}{$minecraftProfile->getMinecraftUUID()}{else}default{/if}-FACE.png" width="96" height="96" alt="" class="userAvatarImage">
				</dt>
				<dd>
					<label><input type="radio" name="avatarType" value="minecraftProfile{$minecraftProfile->getObjectID()}"{if $avatarType == 'minecraftProfile'|concat:$minecraftProfile->getObjectID()} checked{/if}> {lang __encode=true name=$minecraftProfile->getMinecraftName()}wcf.user.avatar.type.minecraftProfile{/lang}</label>
				</dd>
			</dl>
		{/foreach}
	{/if}
{/if}