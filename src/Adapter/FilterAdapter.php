<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Site\Page;

/**
 * Filter a collection of variables by a specified field and value.
 *
 * Sample configuration:
 *
 *  /examples:
 *      template: examples/detail
 *      data:
 *          example: collection.yml
 *          blog: blog.yml
 *      adapters:
 *          filter:
 *              example:
 *                  highlight: true
 *                  title: A
 *              blog:
 *                  highlight: true
 */
class FilterAdapter extends AbstractAdapter
{

    /**
     * @param Page        $page
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapterConfig(AdapterFactory::FILTER_ADAPTER);

        $this->validateConfig($config);

        foreach ($config as $variable => $filters) {
            $entries = $this->getData($page->getVariable($variable));

            foreach ($filters as $field => $value) {
                $entries = array_filter($entries, function ($entry) use ($field, $value) {
                    return isset($entry[$field]) && $entry[$field] == $value;
                });
            }

            $page->setVariableValue($variable, $entries)
                ->setVariableIsParsed($variable);
        }

        /** @var Page[] $result */
        $result = [$page->getId() => $page];

        return $result;
    }

    /**
     * @param array $config
     *
     * @throws ConfigurationException
     */
    private function validateConfig(array $config) {
        if (empty($config)) {
            throw new ConfigurationException('You need to specify at least one field to filter on when using the Filter Adapter.');
        }
    }
}
