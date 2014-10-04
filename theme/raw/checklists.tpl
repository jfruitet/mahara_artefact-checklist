{include file="header.tpl"}
<div id="checklistswrap">
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/checklists/new/item.php">{str section="artefact.checklists" tag="newitem"}</a>
    </div>
{if !$items.data}
    <div class="message">{$strnoitemsaddone|safe}</div>
{else}
<table id="checklistslist">
    <thead>
        <tr>
            <th class="completiondate">{str tag='completiondate' section='artefact.checklist'}</th>
            <th class="checklisttitle">{str tag='title' section='artefact.checklist'}</th>
            <th class="checklistdescription">{str tag='description' section='artefact.checklist'}</th>
			<th class="checklistdescription">{str tag='motivation' section='artefact.checklist'}</th>
            <th class="checklistcontrols"></th>
            <th class="checklistcontrols"></th>
            <th class="checklistcontrols"></th>
        </tr>
    </thead>
    <tbody>
        {$items.tablerows|safe}
    </tbody>
</table>
   {$items.pagination|safe}
{/if}
</div>
{include file="footer.tpl"}
