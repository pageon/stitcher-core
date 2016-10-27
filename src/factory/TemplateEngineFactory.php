<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\engine\smarty\SmartyEngine;
use brendt\stitcher\engine\twig\TwigEngine;
use brendt\stitcher\engine\TemplateEngine;
use brendt\stitcher\exception\UnknownEngineException;

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

        $engine = null;

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
