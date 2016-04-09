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
use Nnx\EntryNameResolver\PhpUnit\TestData\ContextResolver\Custom\Service as CustomService;
use Nnx\EntryNameResolver\ResolverByClassName;

/**
 * Class ResolverByClassNameFunctionalTest
 *
 * @package Nnx\EntryNameResolver\PhpUnit\Test
 */
class ResolverByClassNameFunctionalTest extends AbstractHttpControllerTestCase
{

    /**
     * Данные для тестирования резолвинга на основе конфигов
     *
     * @return array
     */
    public function dataResolveEntryNameByContext()
    {
        return [
            [
                CustomService\Service\TestComponentInterface::class,
                CustomService\Module3\Module::class,
                null
            ],
            [
                CustomService\Service\ComponentInterface::class,
                \stdClass::class,
                CustomService\Service\Component::class
            ],
            [
                \stdClass::class,
                \stdClass::class,
                null
            ],
            [
                \stdClass::class,
                null,
                \stdClass::class
            ],
            [
                'notExistsClassName',
                null,
                null
            ],
            [
                CustomService\Service\ComponentInterface::class,
                CustomService\Module3\Module::class,
                CustomService\Module3\Component::class
            ],
            [
                CustomService\Service\ComponentInterface::class,
                new CustomService\Module3\Module(),
                CustomService\Module3\Component::class
            ]
        ];
    }


    /**
     * Тестирование резолвинга на основе конфига
     *
     * @dataProvider dataResolveEntryNameByContext
     *
     * @param $entryName
     * @param $context
     * @param $resolvedEntryName
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Nnx\EntryNameResolver\Exception\InvalidContextException
     */
    public function testResolveEntryNameByContext($entryName, $context, $resolvedEntryName)
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var ResolverByClassName $resolverByClassName */
        $resolverByClassName = $entryNameResolverManager->get(ResolverByClassName::class);

        $actualResolvedEntryName = $resolverByClassName->resolveEntryNameByContext($entryName, $context);

        static::assertEquals($resolvedEntryName, $actualResolvedEntryName);
    }


    /**
     * Тестирование резолвинга на основе конфига
     *
     * @expectedExceptionMessage Context of type boolean is invalid; Context not string.
     * @expectedException \Nnx\EntryNameResolver\Exception\InvalidContextException
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Nnx\EntryNameResolver\Exception\InvalidContextException
     */
    public function testResolveEntryNameByInvalidContext()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var ResolverByClassName $resolverByClassName */
        $resolverByClassName = $entryNameResolverManager->get(ResolverByClassName::class);

        $resolverByClassName->resolveEntryNameByContext(\stdClass::class, false);
    }


    /**
     * Тестирование резолвинга на основе конфига
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Nnx\EntryNameResolver\Exception\InvalidContextException
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testSetterGetterEntryBodyNamePattern()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var ResolverByClassName $resolverByClassName */
        $resolverByClassName = $entryNameResolverManager->get(ResolverByClassName::class);

        $expectedPattern = 'testPattern';
        $valid = $resolverByClassName === $resolverByClassName->setEntryBodyNamePattern($expectedPattern);
        static::assertTrue($valid);
    }
}
