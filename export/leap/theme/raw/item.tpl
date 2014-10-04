{include file="export:leap:entry.tpl" skipfooter=true}
{if $description}        <leap2:status leap2:stage="{$description}" />{/if}
{if $code}        <leap2:status leap2:stage="{$code}" />{/if}
{if $scale}        <leap2:status leap2:stage="{$scale}" />{/if}
{if $valueindex}        <leap2:status leap2:stage="{$valueindex}" />{/if}
{include file="export:leap:entryfooter.tpl"}
