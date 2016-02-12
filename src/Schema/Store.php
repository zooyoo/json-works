<?php

namespace JohnStevenson\JsonWorks\Schema;

class Store
{
    /**
    * @var array
    */
    protected $data = [];

    public function add($ref, $schema)
    {
        $this->splitRef($ref, $doc, $path);

        if (!$this->matchPath($doc, $path, $partPath)) {
            $this->data[$doc][$path] = $schema;
            return;
        }

        if ($path === $partPath) {
            throw new \RuntimeException($this->getRecursionError($ref));
        }
    }

    public function addRoot($ref, $schema)
    {
        if ($this->hasRoot($ref)) {
            throw new \RuntimeException($this->getRecursionError($ref));
        }

        $this->splitRef($ref, $doc, $path);
        $this->data[$doc] = ['#' => $schema];
    }

    public function hasRoot($ref)
    {
        $this->splitRef($ref, $doc, $path);

        return isset($this->data[$doc]);
    }

    public function makeRef($doc, $path)
    {
        $doc = $doc !== '/' ? $doc : '';

        return sprintf('%s#%s', $doc, $path);
    }

    public function get($ref, &$doc, &$path, &$data)
    {
        $this->splitRef($ref, $doc, $path);

        if (isset($this->data[$doc][$path])) {
            return $this->data[$doc][$path];
        }

        $data = isset($this->data[$doc]) ? $this->getData($doc, $path) : null;
    }

    protected function getData($doc, &$path)
    {
        if (!$this->matchPath($doc, $path, $partPath)) {
            return $this->data[$doc]['#'];
        } else {
            $path = substr($path, strlen($partPath));
            return $this->data[$doc][$partPath];
        }
    }

    protected function matchPath($doc, $path, &$partPath)
    {
        foreach ($this->data[$doc] as $key => $dummy) {
            if (0 === strpos($path, $key)) {
                $partPath = $key;
                return true;
            }
        }

        return false;
    }

    protected function splitRef($ref, &$doc, &$path)
    {
        $parts = explode('#', $ref, 2);

        $doc = $parts[0] ?: '/';
        $path = $parts[1] ?: '#';
    }

    protected function getRecursionError($ref)
    {
        return sprintf('Recursion searching for $ref [%s]', $ref);
    }
}
