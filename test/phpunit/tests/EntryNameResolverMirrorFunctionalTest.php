<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver\PhpUnit\Test;

use Nnx\EntryNameResolver\EntryNameResolverManagerInterface;
use Nnx\EntryNameResolver\PhpUnit\TestData\TestPaths;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Nnx\EntryNameResolver\EntryNameResolverMirror;


/**
 * Class EntryNameResolverMirrorFunctionalTest
 *
 * @package Nnx\EntryNameResolver\PhpUnit\Test
 */
class EntryNameResolverMirrorFunctionalTest extends AbstractHttpControllerTestCase
{
    /**
     * Данные для тестирования работы EntryNameResolverMirror
     *
     * @return array
     */
    public function dataEntryNameResolverMirrorFunctionalTest()
    {
        return [
            ['expectedEntry']
        ];
    }

    /**
     * Проверка работы EntryNameResolverMirror
     *
     * @dataProvider dataEntryNameResolverMirrorFunctionalTest
     *
     * @param $entryName
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testEntryNameResolverMirror($entryName)
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var EntryNameResolverMirror $entryNameResolverMirror */
        $entryNameResolverMirror = $entryNameResolverManager->get(EntryNameResolverMirror::class);

        static::assertEquals($entryName, $entryNameResolverMirror->resolveEntryNameByContext($entryName));
    }
}
