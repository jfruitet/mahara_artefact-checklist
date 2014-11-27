{include file="header.tpl"}
<div id="checklistwrap">
<br /><br />
{if $items.data}
<table id="itemslist" class="fullwidth listing">
<tr>
<td width="90%">
	<h3 align="center"><a href="{$WWWROOT}artefact/checklist/index.php?id={$checklist}">{$artefacttitle|safe}</a></h3>
</td>
<td>
	<a href="{$WWWROOT}artefact/checklist/select/index.php?id={$checklist}" title="{str tag=select}" >
        <img src="{theme_url filename='images/btn_configure.png'}" alt="{str(tag=editspecific arg1=$artefacttitle)|escape:html|safe}"></a>
</td></tr>
</table>
<table id="itemslist" class="fullwidth listing">
    <thead>
        <tr>
            <th width="10%">{str tag='code' section='artefact.checklist'}</th>
            <th width="70%">{str tag='title' section='artefact.checklist'}
			<th width="20%">{str tag='scale' section='artefact.checklist'}</th>
<!--			<th>{str tag='valueindex' section='artefact.checklist'}</th> -->
<!--			<th>{str tag='optionitem' section='artefact.checklist'}</th> -->
        </tr>
    </thead>
    <tbody>
		{foreach from=$items.data item=itemr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$itemr->code|safe}</td>
				{if $itemr->optionitem == 0}
						<td class="normal">{$itemr->title|safe}</td>
				{else}
						{if $itemr->optionitem == 1}
							<td class="optionnal">{$itemr->title|safe}</td>				
						{else}
							<td class="header">{$itemr->title|safe}</td>
						{/if}
				{/if}
				
				<td>{$itemr->scale|clean_html|safe}</td> 
<!--				<td>{$itemr->valueindex|safe}</td>  -->
<!--				<td>{$itemr->optionitem|safe}</td>  -->
			</tr>
			{if $itemr->description}
				<tr>
					<td class="normal" colspan="2"><i>{$itemr->description|safe}</i></td>
				</tr>
			{/if}
		{/foreach}
    </tbody>
</table>
   {$items.pagination|safe}
   <div align="center">{$urlallitems|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
