{extends 'index.tpl'}

{block 'content'}
    {foreach $churches as $church}
        <li>{$church.name}</li>
    {/foreach}
{/block}
