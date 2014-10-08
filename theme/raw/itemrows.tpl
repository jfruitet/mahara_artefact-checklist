
{foreach from=$items.data item=itemr}
            <tr class="{cycle values='r0,r1'}">
                <td>{$itemr->code}</td>
				<td>
				{if $itemr->optionitem == 0}
						{$itemr->title|safe}
				{else}
						{if $itemr->optionitem == 1}
							<i>{$itemr->title|safe}</i>				
						{else}
							<h5>{$itemr->title|safe}</h5>
						{/if}
				{/if}
				</td>
                   
				<td>{$itemr->scale|clean_html|safe}</td>
<!--
				<td>{$itemr->valueindex}</td>
				<td>{$itemr->optionitem}</td>  
-->
            </tr>
			{if $itemr->description}
			<tr>
			    <td colspan="4"><i>{$itemr->description|clean_html|safe}</i></td>    
			</tr>
			{/if}
{/foreach}
