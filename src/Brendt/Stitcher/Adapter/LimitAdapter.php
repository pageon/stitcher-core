<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Site\Page;

class LimitAdapter extends AbstractAdapter
{
    /**
     * @param Page        $page
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transformPage(Page $page, $filter = null) : array
    {
        $config = $page->getAdapterConfig(AdapterFactory::LIMIT_ADAPTER);
        $config = isset($config['variable']) ? [$config['variable'] => $config] : $config;

        foreach ($config as $variable => $limit) {
            $entries = $this->getData($page->getVariable($variable));
            $entries = array_slice($entries, 0, $limit);

            $page->setVariableValue($variable, $entries)
                ->setVariableIsParsed($variable);
        }

        /** @var Page[] $result */
        $result = [$page->getId() => $page];

        return $result;
    }
}
