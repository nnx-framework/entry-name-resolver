<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

use Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use ReflectionClass;



/**
 * Class ResolverByMapFactory
 *
 * @package Nnx\EntryNameResolver\EntryNameResolver
 */
class ResolverByModuleContextMapFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    /**
     * Имя создаваемого класса
     *
     * @var string
     */
    protected static $defaultTargetClassName = ResolverByModuleContextMap::class;

    /**
     * @inheritdoc
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ResolverByModuleContextMap
     * @throws \Nnx\EntryNameResolver\Exception\RuntimeException
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $appServiceLocator = $serviceLocator;
        if ($serviceLocator instanceof AbstractPluginManager) {
            $appServiceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var ModuleOptionsPluginManagerInterface $moduleOptionsPluginManager */
        $moduleOptionsPluginManager = $appServiceLocator->get(ModuleOptionsPluginManagerInterface::class);


        $creationOptions = $this->getCreationOptions();
        $options = is_array($creationOptions) ? $creationOptions : [];

        $contextMap = array_key_exists('contextMap', $options) ? $options['contextMap'] : [];

        $className = array_key_exists('className', $options) ? (string)$options['className'] : static::$defaultTargetClassName;

        $r = new ReflectionClass($className);
        $resolverByModuleContextMap = $r->newInstance($moduleOptionsPluginManager);

        if (!$resolverByModuleContextMap instanceof static::$defaultTargetClassName) {
            $errMsg = sprintf('ResolverByModuleContextMap not implements: %s', static::$defaultTargetClassName);
            throw new Exception\RuntimeException($errMsg);
        }

        $resolverByModuleContextMap->setContextMap($contextMap);

        return $resolverByModuleContextMap;
    }
}
