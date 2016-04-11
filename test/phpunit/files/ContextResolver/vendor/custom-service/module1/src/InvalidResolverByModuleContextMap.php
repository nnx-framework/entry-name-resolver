<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver\PhpUnit\TestData\ContextResolver\Custom\Service\Module1;

use Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface;

/**
 * Class InvalidResolverByModuleContextMap
 *
 * @package Nnx\EntryNameResolver\PhpUnit\TestData\ContextResolver\Custom\Service\Module1
 */
class InvalidResolverByModuleContextMap
{
    /**
     * Менеджер настроек модулей
     *
     * @var \Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface
     */
    protected $moduleOptionsPluginManager;

    /**
     * ResolverByModuleContextMap constructor.
     *
     * @param ModuleOptionsPluginManagerInterface $moduleOptionsPluginManager
     */
    public function __construct(ModuleOptionsPluginManagerInterface $moduleOptionsPluginManager)
    {
        $this->moduleOptionsPluginManager = $moduleOptionsPluginManager;
    }
} 