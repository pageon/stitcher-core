<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\exception\UnknownEngineException;
use brendt\stitcher\template\smarty\SmartyEngine;
use brendt\stitcher\template\TemplateEngine;
use brendt\stitcher\template\twig\TwigEngine;

class TemplateEngineFactory {

    const SMARTY_ENGINE = 'smarty';

    const TWIG_ENGINE = 'twig';

    private $engines;

    /**
     * @param $type
     *
     * @return TemplateEngine
     * @throws UnknownEngineException
     */
    public function getByType($type) {
        if (isset($this->engines[$type])) {
            return $this->engines[$type];
        }

        switch ($type) {
            case self::TWIG_ENGINE:
                $engine = new TwigEngine();

                break;
            case self::SMARTY_ENGINE:
                $engine = new SmartyEngine();

                break;
            default:
                throw new UnknownEngineException();
        }

        if ($engine) {
            $this->engines[$type] = $engine;
        }

        return $engine;
    }

}
