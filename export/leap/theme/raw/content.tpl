{include file="export:leap:entry.tpl" skipfooter=true}

{if $description}        <leap2:status leap2:stage="{$description}" />{/if}
{if $motivation}        <leap2:status leap2:stage="{$motivation}" />{/if}
{if $public}        <leap2:status leap2:stage="{$public}" />{/if}

{include file="export:leap:entryfooter.tpl"}

