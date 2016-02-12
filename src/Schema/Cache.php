<?php

namespace JohnStevenson\JsonWorks\Schema;

use JohnStevenson\JsonWorks\Helpers\Finder;
use JohnStevenson\JsonWorks\Helpers\Patch\Target;
use JohnStevenson\JsonWorks\Schema\DataChecker;

class Cache
{
    /**
    * @var DataChecker
    */
    protected $dataChecker;

    /**
    * @var \JohnStevenson\JsonWorks\Helpers\Finder
    */
    protected $finder;

    /**
    * @var Store
    */
    protected $store;

    /**
    * @var array
    */
    protected $parents;

    public function __construct($schema)
    {
        $this->store = new Store;
        $this->store->addRoot('#', $schema);

        $this->dataChecker = new DataChecker;
        $this->finder = new Finder;
    }

    public function resolveRef($ref)
    {
        if ($this->store->hasRoot($ref)) {
            $this->parents = [];

            return $this->resolve($ref);
        }
    }

    protected function resolve($ref)
    {
        if ($schema = $this->store->get($ref, $doc, $path, $data)) {
            return $schema;
        }

        $this->checkParents($ref);

        if ($schema = $this->find($ref, $doc, $path, $data)) {
            return $schema;
        }

        $error = $this->getRefError('Unable to find $ref', $ref);
        throw new \RuntimeException($error);
    }

    protected function checkParents($ref)
    {
        if (!in_array($ref, $this->parents)) {
            return;
        }

        $error = $this->getRefError('Circular reference found', $this->parents);
        throw new \RuntimeException($error);
    }

    protected function find($ref, $doc, $path, $data)
    {
        $target = new Target($path, $error);

        if ($this->finder->get($data, $target)) {
            return $this->processFoundSchema($ref, $target->element);
        }

        if ($this->dataChecker->checkForRef($target->element, $childRef)) {
            $foundRef = $this->store->makeRef($doc, $target->foundPath);

            return $this->processFoundRef($ref, $foundRef, $childRef);
        }
    }

    protected function processFoundSchema($ref, $schema)
    {
        if ($this->dataChecker->checkForRef($schema, $childRef)) {
            $this->parents[] = $ref;
            $schema = $this->resolve($childRef);
        }

        $this->store->add($ref, $schema);

        return $schema;
    }

    protected function processFoundRef($ref, $foundRef, $childRef)
    {
        $this->parents[] = $foundRef;

        if ($schema = $this->resolve($childRef)) {
            $this->store->add($foundRef, $schema);

            // remove foundRef from parents
            $key = array_search($foundRef, $this->parents);
            $this->parents[$key];
        }

        return $schema;
    }

    protected function getRefError($caption, $ref)
    {
        return sprintf('%s [%s]', $caption, implode(', ', (array) $ref));
    }
}
