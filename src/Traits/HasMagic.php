<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\Node;
use LindenCMS\Core\NodeValue;

trait HasMagic
{
    protected array $magicProps = [];

    public function initMagic(\ReflectionClass $reflection): array
    {
        $this->magicProps = [];

        $getMethodAttributes = function (\ReflectionMethod $method) {
            $propertyAttrs = [];
            foreach ($method->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();
                $propertyAttrs[get_class($attrInstance)] = $attrInstance;
            }

            return $propertyAttrs;
        };

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if (str_starts_with($methodName, '_') && is_subclass_of($method->getReturnType()?->getName(), Node::class)) {
                $propName = str_replace('_', '', $methodName);
                $attributes = $getMethodAttributes($method);
                $this->magicProps[$propName] = [
                    'node' => null,
                    'attributes' => $attributes,
                ];
            }

            // if (!is_subclass_of($method->getReturnType()?->getName(), Node::class)) {
            //     continue;
            // }

            // if (!str_starts_with($methodName, '__')) {
            //     continue;
            // }

            // Skip magic methods that isn't declared in $this class (come from parent/trait)
            // if ($method->getDeclaringClass()->getName() !== $reflection->getName()) {
            //     continue;
            // }

            // if (in_array($methodName, ['__construct', '__destruct', '__call', '__get', '__set'])) {
            //     continue;
            // }

            // if (!is_subclass_of($method->getReturnType()?->getName(), Node::class)) {
            //     continue;
            // }
        }

        return $this->magicProps;
    }

    public function hasMagicProp(string $name): bool
    {
        return isset($this->magicProps[$name]);
    }

    private function getMagicPropState(string $name): mixed
    {
        return $this->magicProps[$name]['node'];
    }

    private function setMagicPropState(string $name, mixed $state)
    {
        $this->magicProps[$name]['node'] = $state;
    }

    private function getMagicPropAttributes(string $name): array
    {
        return $this->magicProps[$name]['attributes'];
    }

    private function callMagicProp(string $name)
    {
        // State from first/last call
        $prevState = $this->getMagicPropState($name);
        
        // Update state
        $newState = $this->{"_$name"}($prevState);
        $newState->nodeAttributes = $this->getMagicPropAttributes($name);
        $newState->setParent($this, $name);
        if ($prevState instanceof NodeValue) {
            $newState->set($prevState?->get());
        }
        
        // Save new state
        $this->setMagicPropState($name, $newState);

        return $newState;
    }

    public function __get($name)
    {
        if (!$this->hasMagicProp($name)) {
            throw new \BadMethodCallException();
        }

        return $this->callMagicProp($name);
    }
}
