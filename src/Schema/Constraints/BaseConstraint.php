<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraints;

use JohnStevenson\JsonWorks\Helpers\Tokenizer;
use JohnStevenson\JsonWorks\Schema\Constraints\Manager;
use JohnStevenson\JsonWorks\Schema\ValidationException;

abstract class BaseConstraint
{
    /**
    * @var Manager
    */
    protected $manager;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Tokenizer
    */
    protected $tokenizer;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->tokenizer = new Tokenizer;
    }

    protected function addError($error)
    {
        $path = $this->tokenizer->encode($this->manager->dataPath) ?: '#';
        $this->manager->errors[] = sprintf("Property: '%s'. Error: %s", $path, $error);

        if ($this->manager->stopOnError) {
            throw new ValidationException();
        }
    }

    public function get($schema, $key, $default = null)
    {
        return $this->manager->get($schema, $key, $default);
    }

    public function getValue($schema, $key, &$value, &$type, $required = null)
    {
        return $this->manager->getValue($schema, $key, $value, $type, $required);
    }

    protected function getSchemaError($expected, $value)
    {
        return $this->manager->getSchemaError($expected, $value);
    }

    protected function match($regex, $string)
    {
        return preg_match('{'.$regex.'}', $string, $match);
    }
}
