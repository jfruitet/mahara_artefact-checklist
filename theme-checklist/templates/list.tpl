{foreach from=$chklist.data item=chklist}
    <div class="{cycle values='r0,r1'} listrow">
            <h3 class="title"><a href="{$WWWROOT}artefact/checklist/checklist.php?id={$chklist->id}">{$chklist->title}</a></h3>
			<div class="detail">{$chklist->description|clean_html|safe}</div>
			<div class="detail">{$chklist->value|clean_html|safe}</div>
			<div class="detail">{$chklist->threshold|clean_html|safe}</div> </p>
            <div class="fr checklisttatus">			
                <a href="{$WWWROOT}artefact/checklist/edit/index.php?id={$chklist->id}" title="{str tag=edit}" >
                    <img src="{$iconedit}" alt="{str(tag=editspecific arg1=$chklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/checklist/checklist.php?id={$chklist->id}" title="{str tag=manageitems section=artefact.checklist}">
                    <img src="{$iconconfigure}" alt="{str(tag=manageitemssspecific section=artefact.checklist arg1=$chklist->title)|escape:html|safe}"></a>
                <a href="{$WWWROOT}artefact/checklist/delete/index.php?id={$chklist->id}" title="{str tag=delete}">
                    <img src="{$icondelete}" alt="{str(tag=deletespecific arg1=$chklist->title)|escape:html|safe}"></a>
            </div>

            <div class="cb"></div>
    </div>
{/foreach}
