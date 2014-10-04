
{include file="header.tpl"}

<div id="checklistwrap">
<br />
<br />
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/checklist/new.php">{str section="artefact.checklist" tag="newchecklist"}</a>
    </div>
{if !$checklist.data}
    <div class="message">{$strnochecklistaddone|safe}</div>
{else}
<div id="checklistlist" class="fullwidth listing">
        {$checklist.tablerows|safe}
</div>
   {$checklist.pagination|safe}
   <div align="center">{$urlalllists|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}

