<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver\PhpUnit\Test;

use Nnx\EntryNameResolver\EntryNameResolverManager;
use Nnx\EntryNameResolver\EntryNameResolverManagerInterface;
use Nnx\EntryNameResolver\PhpUnit\TestData\TestPaths;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class EntryNameResolverManagerFunctionalTest
 *
 * @package Nnx\EntryNameResolver\PhpUnit\Test
 */
class EntryNameResolverManagerFunctionalTest extends AbstractHttpControllerTestCase
{

    /**
     * Создан некорректный плагин
     *
     * @expectedExceptionMessage Plugin of type stdClass is invalid; must implement Nnx\EntryNameResolver\EntryNameResolverInterface
     * @expectedException \Zend\ServiceManager\Exception\RuntimeException
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Zend\ServiceManager\Exception\InvalidServiceNameException
     */
    public function testResolveEntryNameByContext()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $entryNameResolverManager->setInvokableClass('test', \stdClass::class);

        $entryNameResolverManager->get('test');
    }
}
