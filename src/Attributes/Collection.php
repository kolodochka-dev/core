<?php

namespace LindenCMS\Core\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Collection
{
    public function __construct(
        public string $type = '',
    ) {}
}
