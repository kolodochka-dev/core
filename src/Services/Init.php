<?php

namespace LindenCMS\Core\Services;

use LindenCMS\Core\Contracts\InitContract;
use LindenCMS\Core\Node;

class Init implements InitContract
{
    private $typeResolver;

    public function __construct()
    {
        $this->typeResolver = $this->newTypeResolver();
    }

    public function init(Node $node): void
    {
        $reflection = new \ReflectionClass($node);
        $this->initRoot($node, $reflection);
        $this->initChildren($node, $reflection);
        $this->initMagic($node, $reflection);
    }

    private function newTypeResolver()
    {
        return new class() {
            public function isNode(string|object $class)
            {
                if (is_string($class)) {
                    return ($class == Node::class) || is_subclass_of($class, Node::class);
                }

                return $class instanceof Node;
            }

            // public function isCollection(string|object $class)
            // {
            //     if (is_string($class)) {
            //         return ($class == Collection::class) || is_subclass_of($class, Collection::class);
            //     }

            //     return $class instanceof Collection;
            // }

            public function isShouldInitialize(\ReflectionProperty $property, Node $node): bool
            {
                $reflectionType = $property->getType();

                if ($reflectionType instanceof \ReflectionNamedType) {
                    $isNode = $this->isNode($reflectionType->getName());
                } elseif ($reflectionType instanceof \ReflectionUnionType) {
                    foreach ($reflectionType->getTypes() as $unionType) {
                        $isNode = $this->isNode($unionType->getName());
                    }
                }

                return !$property->isInitialized($node) && $isNode;
            }
        };
    }

    private function initRoot(Node $node, \ReflectionClass $reflection)
    {
        $node->nodeAttributes = array_merge(
            $this->getAttributes($reflection),
            $node->nodeAttributes
        );
    }

    private function initChildren(Node $node, \ReflectionClass $reflection): void
    {
        foreach ($reflection->getProperties() as $property) {
            if ($this->typeResolver->isShouldInitialize($property, $node)) {
                $this->initProperty($property, $node);
            }
        }
    }

    private function initMagic(Node $node, \ReflectionClass $reflection): void
    {
        $node->initMagic($reflection);
    }

    private function initProperty(\ReflectionProperty $property, Node $parent): void
    {
        $node = $this->createComponent($property, $parent);
        $property->setValue($parent, $node);
    }

    private function createComponent(\ReflectionProperty $property, Node $parent): Node
    {
        $node = new ($property->getType()->getName());
        $node->setParent($parent, $property->getName());
        $node->nodeAttributes = $this->getAttributes($property);

        if (!$node->isDeferredInit()) {
            $this->init($node);
        }

        return $node;
    }

    private function getAttributes(\ReflectionProperty|\ReflectionClass $property): array
    {
        $propertyAttrs = [];
        foreach ($property->getAttributes() as $attr) {
            $attrInstance = $attr->newInstance();
            $propertyAttrs[get_class($attrInstance)] = $attrInstance;
        }

        return $propertyAttrs;
    }
}
