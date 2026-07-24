<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\ContextDetector;

trait HasAutoContexts
{
    protected ?ContextDetector $contextDetector;

    public static function contextsDir()
    {
        return class_basename(self::class);
    }

    public function detectContextsFrom(): array
    {
        return [];
    }

    public function ctx()
    {
        if (!isset($this->contextDetector)) {
            $this->contextDetector = new ContextDetector($this);
        }
            
        return $this->contextDetector;
    }
}