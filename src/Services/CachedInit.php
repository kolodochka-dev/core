<?php

namespace LindenCMS\Core\Services;

use LindenCMS\Core\Node;

// [
//     'type' => 'App\\Rebuild\\Planet',
//     'attributes' => [],
//     'children' => [
//         'title' => [
//             'type' => 'App\\Rebuild\\_String',
//             'attributes' => [],
//             'children' => []
//         ],
//         'volume' => [
//             'type' => 'App\\Rebuild\\_String',
//             'attributes' => [],
//             'children' => []
//         ],
//         'weight' => [
//             'type' => 'App\\Rebuild\\_String',
//             'attributes' => [],
//             'children' => []
//         ],
//         'is_free' => [
//             'type' => 'App\\Rebuild\\_String',
//             'attributes' => [],
//             'children' => []
//         ],
//     ],
// ]
class CachedInit
{
    public function init(Node $node, $cache)
    {
        foreach ($cache['attributes'] as $attributeClass => $attributeData) {
            $node->nodeAttributes = array_merge(
                [$attributeClass => new $attributeClass(...$attributeData)],
                $node->nodeAttributes
            );
        }

        foreach ($cache['children'] as $propName => $propCache) {
            $prop = new $propCache['type'];
            $this->init($prop, $propCache);
            $node->$propName = $prop;
            $node->addChild($prop, $propName, true);
        }

        return $node;
    }

    private function cachePath(Node $node)
    {
        return "{$node->cacheInitPath()}/" . md5($node::class) . ".php";
    }

    public function isCacheExists(Node $node)
    {
        return file_exists($this->cachePath($node));
    }

    public function getCache(Node $node)
    {
        return require $this->cachePath($node);
    }

    public function createCache(Node $node)
    {
        $eol = PHP_EOL;
        $content = var_export($node->toSchema(), true);

        return file_put_contents($this->cachePath($node), "<?php$eol$eol return $content; $eol");
    }
}
