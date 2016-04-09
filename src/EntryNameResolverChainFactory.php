<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;

/**
 * Class EntryNameResolver
 *
 * @package Nnx\EntryNameResolver\EntryNameResolver
 */
class EntryNameResolverChainFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    /**
     * @inheritdoc
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EntryNameResolverChain
     *
     * @throws Exception\RuntimeException
     *
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     * @throws \Zend\ServiceManager\Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var AbstractPluginManager $serviceLocator */


        $creationOptions = $this->getCreationOptions();
        $options = is_array($creationOptions) ? $creationOptions : [];

        $resolvers = array_key_exists('resolvers', $options) && is_array($options['resolvers']) ? $options['resolvers'] : [];

        $chain = new EntryNameResolverChain();
        foreach ($resolvers as $entryNameResolverConfig) {
            if (!is_array($entryNameResolverConfig)) {
                $errMsg = 'Entry name resolver config is not array';
                throw new Exception\RuntimeException($errMsg);
            }

            if (!array_key_exists('name', $entryNameResolverConfig)) {
                $errMsg = 'Resolver entry name not found';
                throw new Exception\RuntimeException($errMsg);
            }

            $name = $entryNameResolverConfig['name'];

            $resolverOptions = array_key_exists('options', $entryNameResolverConfig) ? $entryNameResolverConfig['options'] : [];

            if (!is_array($resolverOptions)) {
                $errMsg = 'Resolver options is not array';
                throw new Exception\RuntimeException($errMsg);
            }

            /** @var EntryNameResolverInterface $resolver */
            $resolver = $serviceLocator->get($name, $resolverOptions);

            $priority = array_key_exists('priority', $entryNameResolverConfig) ? (integer)$entryNameResolverConfig['priority'] : EntryNameResolverChain::DEFAULT_PRIORITY;

            $chain->attach($resolver, $priority);
        }

        return $chain;
    }
}
