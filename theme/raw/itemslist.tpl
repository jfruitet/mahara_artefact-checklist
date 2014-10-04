{foreach from=$items.data item=itemr}
	<tr class="{cycle values='r0,r1'}">
            <td>{$itemr->code}</td>
            <td>{$itemr->title}</td>
            <td>{$itemr->scale|clean_html|safe}</td>			
			<td>{$itemr->valueindex}</td>
		
	</tr>
	<tr>
			<td colspan="3"><i>{$itemr->description|clean_html|safe}</i></td>    
            <td class="buttonscell btns2 checklistcontrols">
                <a href="{$WWWROOT}artefact/checklist/edit/item.php?id={$itemr->item}" title="{str tag=edit}">
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/checklist/delete/item.php?id={$itemr->item}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$itemr->title)|escape:html|safe}">
                </a>
            </td>		
	</tr>
{/foreach}
