
{if $items.data}
<table id="chktable_{$blockid}" class="checklistblocktable fullwidth">
    <thead>
        <tr>
            <th class="c1">{str tag='description' section='artefact.checklist'}</th>
            <th class="c2">{str tag='motivation' section='artefact.checklist'}</th>
        </tr>
    </thead>
    <tbody>
    <tr>
			<td>{if $chkdescription} {$chkdescription|clean_html|safe}{/if}</td>
            <td>{if $chkmotivation} {$chkmotivation|clean_html|safe}{/if}</td>
    </tr>
    </tbody>
</table>
<hr>
<table id="itemtable_{$blockid}" class="checklistblocktable fullwidth">

    <thead>
        <tr>
            <th class="c1">{str tag='code' section='artefact.checklist'}</th>
            <th class="c2">{str tag='title' section='artefact.checklist'}</th>
            <th class="c4">{str tag='scale' section='artefact.checklist'}</th>
			{if $itemr->description}
				<tr>
					<th class="c5" colspan="4">
					{str tag='description' section='artefact.checklist'}
					</th>
			{/if}
		</tr>
    </thead>
    <tbody>
		{foreach from=$items.data item=itemr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$itemr->code|safe}</td>
				<td>
				{if $itemr->optionitem == 0}
						{$itemr->title|safe}
				{else}
						{if $itemr->optionitem == 1}
							<i>{$itemr->title|safe}</i>				
						{else}
							<h5>{$itemr->title|safe}</h5>
						{/if}
				{/if}
				</td>

				<td>{$itemr->scale|clean_html|safe}</td>
<!--
				<td>{$itemr->valueindex|safe}</td>
-->
			{if $itemr->description}
			<tr>
				<td colspan="4"><i>{$itemr->description|clean_html|safe}</i></td>    
			{/if}
			</tr>
		{/foreach}
    </tbody>
</table>
{if $items.pagination}
<div id="checklist_page_container_{$blockid}" class="nojs-hidden-block">{$items.pagination|safe}</div>
{/if}
{else}
    <p>{str tag='noitems' section='artefact.checklist'}</p>
{/if}
