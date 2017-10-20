```yaml
// entries.yaml

a:
    title: A
    image: 
        src: image.jpg
        alt: test
    body: body.md
    
// site.yaml

/:
    variables:
        entries: entries.yaml
```

- foreach `site.yaml` as `pageId` => `pageConfig`
- foreach `pageConfig['variables']` as `variableName` => `variableConfig`
- `variableName` = parse(`variableConfig`)

- foreach `variableConfig` as `propertyName` => `propertyConfig`
- if factory(`propertyConfig`) > `variableName` = factory(`propertyConfig`)->parse
- elseif is_array(`propertyConfig`) > foreach `propertyConfig` as `childPropertyName` => `childProperyConfig`
- else `variableName` = `propertyConfig`
