
<div id="checklistwrap">
{foreach from=$checklists.data item=checklist}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/checklist/viewlist.php?id={$checklist->id}">{$checklist->title}</a></h3>
 
            <div class="fr checkliststatus">
                <a href="{$WWWROOT}artefact/checklist/select/index.php?id={$checklist->id}" title="{str tag=select}" >
                    <img src="{$iconconfigure}" alt="{str(tag=editspecific arg1=$checklist->title)|escape:html|safe}"></a>
             </div>

			<div class="detail">{$checklist->description|clean_html|safe} {$checklist->motivation|clean_html|safe}</div>

            <div class="cb"></div>
    </div>
{/foreach}
{$checklists.pagination|safe}
</div>

  
