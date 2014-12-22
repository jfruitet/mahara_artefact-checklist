{include file="header.tpl"}
<div id="checklistwrap">
<br /><br /><br />

    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/checklist/new.php?id={$checklist}">{str section="artefact.checklist" tag="newitem"}</a>
    </div>
    {if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
{if !$items.data}
    <div>{$checklistitemsdescription}</div>
    <div class="message">{$strnoitemsaddone|safe}</div>
{else}
<table id="itemslist" class="fullwidth listing">
<tr><td width="90%">
<h3 align="center"><a href="{$WWWROOT}artefact/checklist/index.php?id={$checklist}">{$artefacttitle|safe}</a></h3>
{if $artefactdescription}
<p>{$artefactdescription|safe}
{if $artefactmotivation}
<br />{$artefactmotivation|safe}
{/if}
</p>
{/if}
</td>
</tr></table>
<table id="itemslist" class="fullwidth listing">
    <thead>
        <tr>
            <th width="10%">{str tag='code' section='artefact.checklist'}</th>
            <th width="70%">{str tag='title' section='artefact.checklist'}
			<th width="10%">{str tag='scale' section='artefact.checklist'}</th>
<!--			<th>{str tag='valueindex' section='artefact.checklist'}</th> -->
<!--			<th>{str tag='optionitem' section='artefact.checklist'}</th> -->
<th with="10%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		{foreach from=$items.data item=itemr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$itemr->code|safe}</td>
				{if $itemr->optionitem == 0}
						<td class="normal">{$itemr->title|safe}</td>
				{else}
						{if $itemr->optionitem == 1}
							<td class="optionnal">{$itemr->title|safe}</td>				
						{else}
							<td class="header">{$itemr->title|safe}</td>
						{/if}
				{/if}
				
				<td>{$itemr->scale|clean_html|safe}</td>
<!--				<td>{$itemr->valueindex|safe}</td>  -->
<!--				<td>{$itemr->optionitem|safe}</td>  -->
				<td class="buttonscell btns2 planscontrols"> 
 				
                <a href="{$WWWROOT}artefact/checklist/edit/item.php?id={$itemr->item}" title="{str tag=edit}">
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/checklist/delete/item.php?id={$itemr->item}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$itemr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/checklist/checklist.php?id={$itemr->parent}&amp;itemid={$itemr->item}&amp;direction=1&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=moveitemdown}">
                    <img src="{theme_url filename='images/btn_movedown.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/checklist/checklist.php?id={$itemr->parent}&amp;itemid={$itemr->item}&amp;direction=0&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=moveitemup}">
                    <img src="{theme_url filename='images/btn_moveup.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>		
                <a href="{$WWWROOT}artefact/checklist/new.php?id={$itemr->parent}&amp;positionafter={$itemr->displayorder}" title="{str tag=insertitemafter section=artefact.checklist}">
                    <img src="{theme_url filename='images/btn_add.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>

				</td>				

			</tr>
			<tr>
				<td class="normal" colspan="4"><i>{$itemr->description|safe}</i></td>
			</tr>
		{/foreach}
    </tbody>
</table>
   {$items.pagination|safe}
   <div align="center">{$urlallitems|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
