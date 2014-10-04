
{foreach from=$items.data item=itemr}
            <tr class="{cycle values='r0,r1'}">
                <td>{$itemr->code}</td>
				<td>{$itemr->title}</td>                      
				<td>{$itemr->scale}</td>
				<td>{$itemr->valueindex}</td>
            </tr>
			{if $itemr->description}
			<tr>
			    <td colspan="4"><i>{$itemr->description|clean_html|safe}</i></td>    
			</tr>
			{/if}
{/foreach}
