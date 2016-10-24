{extends 'index.tpl'}

{block 'content'}
    {foreach $collection as $entry}
        <h2>{$entry.title}</h2>
        <p>{$entry.intro}</p>
        <a href="/examples/{$entry.id}">Read more</a>
    {/foreach}
{/block}
