{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftProfileList'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftProfileList{/lang}</h1>
    </div>
</header>

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
                    <th></th>
                    <th>{lang}wcf.global.objectID{/lang}</th>
                    <th>{lang}wcf.acp.page.minecraftProfileList.minecraftID{/lang}</th>
                    <th>{lang}wcf.acp.page.minecraftProfileList.minecraftUUID{/lang}</th>
                    <th>{lang}wcf.acp.page.minecraftProfileList.minecraftName{/lang}</th>
                    <th>{lang}wcf.acp.page.minecraftProfileList.hasGeneratedImage{/lang}</th>
                    <th>{lang}wcf.acp.page.minecraftProfileList.isOnline{/lang}</th>
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
                        <td class="columnID">{#$object->getMinecraftID()}</td>
                        <td class="columnTitle">{$object->getMinecraftUUID()}</td>
						<td class="columnUsername">{$object->getMinecraftName()}</td>
						<td class="columnBoolean">
							{if $object->hasGeneratedImage()}
								{lang}wcf.global.form.boolean.yes{/lang}
							{else}
								{lang}wcf.global.form.boolean.no{/lang}
							{/if}
						</td>
						<td class="columnBoolean">
							{if $object->isOnline()}
								{lang}wcf.global.form.boolean.yes{/lang}
							{else}
								{lang}wcf.global.form.boolean.no{/lang}
							{/if}
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
