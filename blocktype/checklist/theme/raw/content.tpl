
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
			<td>{if $chkdescription} {$chkdescription}{/if}</td>
            <td>{if $chkmotivation} {$chkmotivation}{/if}</td>
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
<!--
			<th class="c5">{str tag='validated' section='artefact.checklist'}</th>
-->
        </tr>
    </thead>
    <tbody>
		{foreach from=$items.data item=itemr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$itemr->code|safe}</td>
				<td>{$itemr->title|safe}</td>
				<td>{$itemr->scale|safe}</td>
<!--
				<td>{$itemr->valueindex|safe}</td>
-->
			</tr>
			{if $itemr->description}
			<tr>
				<td colspan="4"><i>{$itemr->description|clean_html|safe}</i></td>    
			</tr>
			{/if}
		{/foreach}
    </tbody>
</table>
{if $items.pagination}
<div id="checklist_page_container_{$blockid}" class="nojs-hidden-block">{$items.pagination|safe}</div>
{/if}
{else}
    <p>{str tag='noitems' section='artefact.checklist'}</p>
{/if}
