{if $imageType !== null}
<dl{if $errorField === 'imageType'} class="formError"{/if}>
	<dt><label for="imageType">{lang}wcf.global.imageType{/lang}</label></dt>
	<dd>
		<select name="imageType" id="imageType">
			<option value="FACE"{if $imageType == 'FACE'} selected{/if}>{lang}wcf.global.imageType.face{/lang}</option>
			<option value="FRONT"{if $imageType == 'FRONT'} selected{/if}>{lang}wcf.global.imageType.front{/lang}</option>
		</select>
		
		{if $errorField === 'imageType'}
			<small class="innerError">
				{lang}wcf.global.imageType.error.{$errorType}{/lang}
			</small>
		{/if}
	</dd>
</dl>
{/if}
{if $imageWidth !== null}
<dl{if $errorField === 'imageWidth'} class="formError"{/if}>
	<dt><label for="imageWidth">{lang}wcf.global.imageWidth{/lang}</label></dt>
	<dd>
		<input type="number" name="imageWidth" id="imageWidth" value="{$imageWidth}" min="1" class="tiny">
		{if $errorField === 'imageWidth'}
			<small class="innerError">
				{if $errorType === 'greaterThan'}
					{lang greaterThan=0}wcf.global.form.error.greaterThan{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>
{/if}