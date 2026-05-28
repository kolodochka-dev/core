<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\Attributes\Collection;

trait HasAttributes
{
    public function _collection(): ?Collection
    {
        return $this->nodeAttributes[Collection::class] ?? null;
    }
}
