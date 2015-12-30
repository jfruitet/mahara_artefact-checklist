{foreach from=$checklists.data item=checklist}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/checklist/checklist.php?id={$checklist->id}">{$checklist->title}</a></h3>
 
            <div class="fr checkliststatus">
                <a href="{$WWWROOT}artefact/checklist/valide/index.php?id={$checklist->id}" title="{str tag=validate section=artefact.checklist}" >
                    <img src="{$iconcheckpath}" alt="{str(tag=validatespecific section=artefact.checklist arg1=$checklist->title)|escape:html|safe}"></a>

                <a href="{$WWWROOT}artefact/checklist/edit/index.php?id={$checklist->id}" title="{str tag=edit}" >
                    <img src="{$iconedit}" alt="{str(tag=editspecific arg1=$checklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/checklist/checklist.php?id={$checklist->id}" title="{str tag=manageitems section=artefact.checklist}">
                    <img src="{$iconconfigure}" alt="{str(tag=manageitemsspecific section=artefact.checklist arg1=$checklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/checklist/export/index.php?id={$checklist->id}" title="{str tag=export section=artefact.checklist}" >
                    <img src="{$iconexport}" alt="{str(tag=exportspecific  section=artefact.checklist arg1=$checklist->title)|escape:html|safe}"></a>			
				<a href="{$WWWROOT}artefact/checklist/delete/index.php?id={$checklist->id}" title="{str tag=delete}">
                    <img src="{$icondelete}" alt="{str(tag=deletespecific arg1=$checklist->title)|escape:html|safe}"></a>
            
			</div>

			<div class="detail">{$checklist->description|safe} {$checklist->motivation|safe}</div>

            <div class="cb"></div>
    </div>
{/foreach}

