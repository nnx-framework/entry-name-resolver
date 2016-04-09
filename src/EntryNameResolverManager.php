<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception;

/**
 * Class EntryNameResolverManager
 *
 * @package Nnx\EntryNameResolver\EntryNameResolver
 *
 * @method EntryNameResolverInterface get($name, $options = array(), $usePeeringServiceManagers = true)
 */
class EntryNameResolverManager extends AbstractPluginManager implements EntryNameResolverManagerInterface
{
    /**
     * Имя секции в конфиги приложения отвечающей за настройки менеджера
     *
     * @var string
     */
    const CONFIG_KEY = 'nnx_entry_name_resolver';

    /**
     * EntryNameResolverManager constructor.
     *
     * @param null|ConfigInterface $configuration
     *
     * @throws \Zend\ServiceManager\Exception\RuntimeException
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->setShareByDefault(false);
    }


    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof EntryNameResolverInterface) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            EntryNameResolverInterface::class
        ));
    }
}
