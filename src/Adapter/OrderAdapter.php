<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Site\Page;

/**
 * Order a collection of variables by a specified field.
 *
 * Sample configuration:
 *
 *  /examples:
 *      template: examples/detail
 *      data:
 *          example: collection.yml
 *      adapters:
 *          order:
 *              variable: example
 *              field: id
 *              direction: desc
 */
class OrderAdapter extends AbstractAdapter
{

    private static $reverse = [
        'desc',
        'DESC',
        '-',
    ];

    /**
     * @param Page        $page
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapterConfig(AdapterFactory::ORDER_ADAPTER);

        $this->validateConfig($config);
        $variable = $config['variable'];
        $field = $config['field'];
        $direction = isset($config['direction']) ? $config['direction'] : null;

        $entries = $this->getData($page->getVariable($variable));

        uasort($entries, function ($a, $b) use ($field) {
           return strcmp($a[$field], $b[$field]);
        });

        if (in_array($direction, self::$reverse)) {
            $entries = array_reverse($entries, true);
        }

        $page->setVariableValue($variable, $entries)
            ->setVariableIsParsed($variable);
        
        return [$page->getId() => $page];
    }

    private function validateConfig(array $config) {
        if (!isset($config['variable']) || !isset($config['field'])) {
            throw new ConfigurationException('Both the configuration entry `field` and `variable` are required when using the Order adapter.');
        }
    }
}
