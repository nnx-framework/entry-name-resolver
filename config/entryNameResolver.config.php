<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

return [
    EntryNameResolverManager::CONFIG_KEY => [
        'invokables'         => [

        ],
        'factories'          => [
            EntryNameResolverChain::class     => EntryNameResolverChainFactory::class,
            ResolverByModuleContextMap::class => ResolverByModuleContextMapFactory::class,
            ResolverByClassName::class => ResolverByClassNameFactory::class
        ],
        'abstract_factories' => [

        ]
    ],
];


