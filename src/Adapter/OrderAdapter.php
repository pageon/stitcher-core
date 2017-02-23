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
 *              variableName:
 *                  field: id
 *                  direction: desc
 */
class OrderAdapter extends AbstractAdapter
{

    /**
     * A collection of keywords which would reverse the data set.
     *
     * @var array
     */
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
    public function transformPage(Page $page, $filter = null) : array {
        $config = $page->getAdapterConfig(AdapterFactory::ORDER_ADAPTER);
        $config = isset($config['variable']) ? [$config['variable'] => $config] : $config;

        foreach ($config as $variable => $order) {
            $this->validateConfig($order);

            $field = $order['field'];
            $direction = $order['direction'] ?? null;

            $entries = $this->getData($page->getVariable($variable));

            uasort($entries, function ($a, $b) use ($field) {
                return strcmp($a[$field], $b[$field]);
            });

            if (in_array($direction, self::$reverse)) {
                $entries = array_reverse($entries, true);
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
        if (!isset($config['field'])) {
            throw new ConfigurationException('The configuration entry `field` is required when using the Order adapter.');
        }
    }
}
