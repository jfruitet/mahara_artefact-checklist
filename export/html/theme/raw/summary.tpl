{if $checklists}
<ul>
{foreach from=$checklists item=checklist}
    <li><a href="{$checklist.link}">{$checklist.title}</a></li>
{/foreach}
</ul>
{/if}
