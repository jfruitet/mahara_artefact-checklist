
{include file="header.tpl"}

<div id="checklistwrap">
<br />
<br />
<br />
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/checklist/new.php">{str section="artefact.checklist" tag="newchecklist"}</a>
    </div>
{if !$checklists.data}
    <div class="message">{$strnochecklistaddone|safe}</div>
{else}
<div id="checklistlist" class="fullwidth listing">
        {$checklists.tablerows|safe}
</div>
   {$checklists.pagination|safe}
   <div align="center">{$urlalllists|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
