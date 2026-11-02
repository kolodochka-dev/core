<?php

namespace LindenCMS\Core;

class ContextDetector
{
    protected array $locations;
    protected array $contexts;

    public function __construct(
        private Node $node
    ) {
        $this->locations = $this->mergeLocations($node->detectContextsFrom());
    }

    private function mergeLocations($locations): array
    {
        array_push($locations, 'LindenCMS\Core\Contexts');
        return $locations;
    }

    public function __call($name, mixed $data)
    {
        return $this->callContext($name, $data);
    }

    protected function callContext(string $name, array $data)
    {
        $context = $this->detectContext($name);

        if (!$context) {
            throw new \Exception("Callable context '$name' for class " . $this->node::class . " isn't found!");
        }

        return $this->invokeContext($context, $data);
    }

    protected function invokeContext(callable $context, array $data): mixed
    {
        $ref = new \ReflectionFunction(\Closure::fromCallable($context));
        $params = $ref->getParameters();

        if ($params === []) {
            return $context();
        }

        $args = [];
        foreach ($params as $p) {
            $pName = $p->getName();

            if (array_key_exists($pName, $data)) {
                $args[] = $data[$pName];
            } elseif ($p->isOptional()) {
                $args[] = $p->getDefaultValue();
            }
        }
        
        if ($args) {
            return $context(...$args);
        }
        return $context(...$data);
    }

    protected function detectContext(string $name): ?callable
    {
        if (isset($this->contexts[$name])) {
            return $this->contexts[$name];
        }

        $parts = array_map('ucfirst', explode('_', $name));
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
                    $this->contexts[$name] = $instance;

                    return $instance;
                }
            }
        }

        return null;
    }
}
