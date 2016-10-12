{extends 'index.tpl'}

{block 'content'}
    {foreach $churches as $church}
        <li>
            {$church.name}
            {if isset($church.image)}
                <img src="{$church.image.src}" srcset="{$church.image.srcset}">
            {/if}
        </li>
    {/foreach}
{/block}
