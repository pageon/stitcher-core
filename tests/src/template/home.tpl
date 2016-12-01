{extends 'index.tpl'}

{block 'content'}
    {$content}

    {image src='img/blue.jpg' var='image'}
    <img src="{$image.src}" srcset="{$image.srcset}" alt="">

    {foreach $churches as $church}
        <li>
            {$church.name}
            {if isset($church.image)}
                <img src="{$church.image.src}" srcset="{$church.image.srcset}">
            {/if}
        </li>
    {/foreach}
{/block}
