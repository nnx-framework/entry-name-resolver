<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

use Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface;

/**
 * Class ResolverByModuleContextMap
 *
 * @package Nnx\EntryNameResolver
 */
class ResolverByModuleContextMap extends AbstractResolverMap
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
        $this->setModuleOptionsPluginManager($moduleOptionsPluginManager);
    }

    /**
     * @inheritdoc
     *
     * @param $context
     *
     * @return string|null
     *
     * @throws Exception\InvalidContextException
     */
    public function buildContextKey($context)
    {
        $className = $context;
        if (is_object($context)) {
            $className = get_class($context);
        }

        if (!is_string($className)) {
            $errMsg = sprintf(
                'Context of type %s is invalid; Context not string.',
                (is_object($context) ? get_class($context) : gettype($context))
            );
            throw new Exception\InvalidContextException($errMsg);
        }

        $contextKey = null;
        $moduleOptionsPluginManager = $this->getModuleOptionsPluginManager();
        if ($moduleOptionsPluginManager->hasModuleNameByClassName($className)) {
            $contextKey = $moduleOptionsPluginManager->getNormalizeModuleNameByClassName($className);
        }

        return $contextKey;
    }

    /**
     * Возвращает менеджер настроек модулей
     *
     * @return ModuleOptionsPluginManagerInterface
     */
    public function getModuleOptionsPluginManager()
    {
        return $this->moduleOptionsPluginManager;
    }

    /**
     * Устанавливает менеджер настроек модулей
     *
     * @param ModuleOptionsPluginManagerInterface $moduleOptionsPluginManager
     *
     * @return $this
     */
    public function setModuleOptionsPluginManager(ModuleOptionsPluginManagerInterface $moduleOptionsPluginManager)
    {
        $this->moduleOptionsPluginManager = $moduleOptionsPluginManager;

        return $this;
    }
}
