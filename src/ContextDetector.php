<?php

namespace LindenCMS\Core;

class ContextDetector
{
    protected array $locations;
    protected array $contexts;

    public function __construct(
        private Node $node
    ) {
        $this->locations = $node->detectContextsFrom();
    }

    public function __call($name, mixed $data)
    {
        return $this->callContext($name, $data);
    }

    protected function callContext(string $name, mixed $data)
    {
        if (isset($this->contexts[$name])) {
            $context = $this->contexts[$name];
        } else {
            $context = $this->detectContext($name);
            $this->contexts[$name] = $context;
        }

        if (!$context) {
            throw new \Exception("Callable context $name isn't found!");
        }

        return $context($data);
    }

    protected function detectContext(string $name): ?callable
    {
        $parts = array_map('ucfirst', preg_split('/(?=[A-Z])/', $name));
        $path = implode('/', $parts);
        $classBuilders = [
            fn($namespace) => implode('\\', [
                $namespace,
                class_basename($this->node),
                str_replace('/', '\\', $path)
            ]),
            fn($namespace) => implode('\\', [
                $namespace,
                $this->node::class::contextsDir(),
                str_replace('/', '\\', $path)
            ]),
        ];

        foreach ($this->locations as $namespace) {
            foreach ($classBuilders as $classBuidler) {
                $class = $classBuidler($namespace);
                if (class_exists($class)) {
                    $instance = new $class($this->node);
                    return $instance;
                }
            }
        }

        return null;
    }
}