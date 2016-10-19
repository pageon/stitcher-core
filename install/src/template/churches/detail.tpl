{extends 'index.tpl'}

{block 'content'}
    {$intro}

    <h1>
        {$church.name}
    </h1>
    <p>
        {$church.description}
    </p>
{/block}
