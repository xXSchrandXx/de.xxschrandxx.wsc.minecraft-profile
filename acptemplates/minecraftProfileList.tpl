{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftProfileList'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftProfileList{/lang}{if $minecraftID} ({$minecraftTitle}){/if}</h1>
    </div>
        {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                    {if $objects}
                        <li>
                            <a href="{link controller='MinecraftProfileClear'}{if $minecraftID}minecraftID={$minecraftID}{/if}{/link}" class="button">{icon name='xmark'} <span>{lang}wcf.acp.page.minecraftProfileList.clear{/lang}</span></a>
                        </li>
                        <li>
                            <a href="{link controller='MinecraftProfileOffline'}{if $minecraftID}minecraftID={$minecraftID}{/if}{/link}" class="button">{icon name='broom'} <span>{lang}wcf.acp.page.minecraftProfileList.offline{/lang}</span></a>
                        </li>
                    {/if}
                    {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
    {/hascontent}
</header>

<form method="post" action="{link controller='MinecraftProfileList'}{/link}">
    <section class="section">
        <h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
        <div class="row rowColGap formGrid">
			<dl class="col-xs-12 col-md-4">
				<dt></dt>
				<dd>
					<select name="minecraftID" id="minecraftID">
						<option value="0">{lang}wcf.global.language.noSelection{/lang}</option>
						{foreach from=$minecraftList item=minecraft}
							<option value="{$minecraft->getObjectID()}"{if $minecraft->getObjectID() == $minecraftID} selected{/if}>{$minecraft->getTitle()}</option>
						{/foreach}
					</select>
				</dd>
			</dl>
        </div>
        <div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
			{csrfToken}
		</div>
    </section>
</form>

{hascontent}
<div class="paginationTop">
    {content}
        {pages print=true assign=pagesLinks controller="MinecraftProfileList" link="pageNo=%d"}
    {/content}
</div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\minecraft\MinecraftProfileAction">
            <thead>
                <tr>
                    <th class="columnID {if $sortField == 'profileID'}active {$sortOrder}{/if}" colspan="2">
                        <a href="{link controller='MinecraftProfileList'}sortField=profileID{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.global.objectID{/lang}
                        </a>
                    </th>
                    <th class="columnInteger {if $sortField == 'minecraftID'} active {$sortOrder}{/if}">
                        <a href="{link controller='MinecraftProfileList'}sortField=minecraftID{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.acp.page.minecraftProfileList.minecraftID{/lang}
                        </a>
                    </th>
                    <th class="columnTitle {if $sortField == 'minecraftUUID'} active {$sortOrder}{/if}">
                        <a href="{link controller='MinecraftProfileList'}sortField=minecraftUUID{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.acp.page.minecraftProfileList.minecraftUUID{/lang}
                        </a>
                    </th>
                    <th class="columnUsername {if $sortField == 'minecraftName'} active {$sortOrder}{/if}">
                        <a href="{link controller='MinecraftProfileList'}sortField=minecraftName{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.acp.page.minecraftProfileList.minecraftName{/lang}
                        </a>
                    </th>
                    <th class="columnBoolean {if $sortField == 'imageGenerated'} active {$sortOrder}{/if}">
                        <a href="{link controller='MinecraftProfileList'}sortField=imageGenerated{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.acp.page.minecraftProfileList.hasGeneratedImage{/lang}
                        </a>
                    </th>
                    <th class="columnIcon {if $sortField == 'online'} active {$sortOrder}{/if}">
                        <a href="{link controller='MinecraftProfileList'}sortField=online{if $minecraftID}&minecraftID={$minecraftID}{/if}{/link}">
                            {lang}wcf.acp.page.minecraftProfileList.isOnline{/lang}
                        </a>
                    </th>
                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=object}
                    <tr class="jsObjectActionObject" data-object-id="{@$object->getObjectID()}">
                        <td class="columnIcon">
                            {objectAction action="delete" objectTitle=$object->getMinecraftName()}
                            {event name='rowButtons'}
                        </td>
                        <td class="columnID">{#$object->getObjectID()}</td>
                        <td class="columnInteger">{#$object->getMinecraftID()}</td>
                        <td class="columnTitle">{$object->getMinecraftUUID()}</td>
                        <td class="columnUsername">{$object->getMinecraftName()}</td>
                        <td class="columnBoolean">
                            {if $object->hasGeneratedImage()}
                                {lang}wcf.global.form.boolean.yes{/lang}
                            {else}
                                {lang}wcf.global.form.boolean.no{/lang}
                            {/if}
                        </td>
                        <td class="columnIcon">
                            {objectAction action="toggle" isDisabled=$object->isOffline()}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
