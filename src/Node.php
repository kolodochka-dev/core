<?php

namespace LindenCMS\Core;

use LindenCMS\Core\Factories\NodeFactory;
use LindenCMS\Core\Traits\HasMagic;
use Illuminate\Support\Str;
use LindenCMS\Core\Traits\HasAttributes;
use LindenCMS\Core\Traits\HasContexts;
use LindenCMS\Core\Traits\HasTreeStructure;

abstract class Node
{
    use HasTreeStructure, HasAttributes, HasContexts, HasMagic;

    public array $nodeAttributes = [];
    protected readonly string $uid;
    public const int RECURSION_WALK_STATE_BREAK = -1;
    protected readonly string $code;

    public static function make(?Node $parent = null, string $parentPropertyName = ''): static
    {
        $nodeFactory = app(NodeFactory::class);

        return $nodeFactory->create(
            className: static::class,
            parent: $parent,
            parentPropertyName: $parentPropertyName,
        );
    }

    public function toSnakeCase()
    {
        return Str::of(class_basename($this))->snake()->toString();
    }

    public function isDeferredInit()
    {
        return false;
    }

    // public function afterInit()
    // {
    //     // foreach ($this->magicProps as $name => $prop) {
    //     //     $this->callMagicProp($name);
    //     // }
    // }

    public function getUid()
    {
        if (!isset($this->uid)) {
            $this->uid = 'uid' . bin2hex(random_bytes(16));
        }
        
        return $this->uid;
    }

    public function setUid(string $uid)
    {
        $this->uid = $uid;
    }

    public function isEqualTo(Node $node)
    {
        return $this->getUid() === $node->getUid();
    }

    public function fill(mixed $data): static
    {
        if ($data) {
            foreach ($data as $key => $value) {
                if ($this->hasChild($key)) {
                    $this->{$key}->fill($value);
                }
            }
        }

        return $this;
    }

    // TODO: do
    // public function toArray(bool $withNested = false, array $exclude = []): mixed
    // {
    //     foreach ($this->props(fn ($item) => NodeValue::matchType($item)) as $key => $instance) {
    //         if (!in_array($key, $exclude)) {
    //             $data[$instance->getParentPropertyName()] = $instance->toArray();
    //         }
    //     }

    //     return $data;
    // }

    public static function matchType($instance): bool
    {
        return $instance instanceof static;
    }

    public function recursionWalk(callable $callback, bool $walkCollection = false)
    {
        if ($callback($this) == self::RECURSION_WALK_STATE_BREAK) {
            return self::RECURSION_WALK_STATE_BREAK;
        }

        foreach ($this->getChildren() as $field) {
            $field->recursionWalk($callback, $walkCollection);
        }
    }

    public function props(\Closure $filter): array
    {
        return array_filter($this->getChildren(), $filter);
    }

    public function prop(\Closure $filter): ?Node
    {
        $filtered = array_filter($this->getChildren(), $filter);
        
        return array_shift($filtered);
    }

    public function formName(): string
    {
        return dot_to_html_name($this->getPath());
    }
}
