<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\Contexts\Context;

trait HasContexts
{
    protected $contexts = [];
    protected $contextsInstances = [];

    protected function extendContexts(): array
    {
        return [];
    }

    protected function getContext($key): ?Context
    {
        $contextClass = array_merge($this->contexts, $this->extendContexts())[$key] ?? null;
        if (!$contextClass) {
            return null;
        }
        
        if (!isset($this->contextsInstances[$contextClass])) {
            $this->contextsInstances[$contextClass] = app($contextClass);
            $this->contextsInstances[$contextClass]->setNode($this);
            $this->contextsInstances[$contextClass]->setContextName($key);
        }

        return $this->contextsInstances[$contextClass];
    }

    public function context(string $key, array $data = []): mixed
    {
        if (!$context = $this->getContext($key)) {
            throw new \Exception("Context '$key' for class " . $this::class . " not found!");
        }
        
        $context->setData($data);

        return $context->after($context());
    }

    public function hasContext(string $key): bool
    {
        return $this->getContext($key) !== null;
    }
}
