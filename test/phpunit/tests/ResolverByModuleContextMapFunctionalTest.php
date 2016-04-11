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
use Nnx\EntryNameResolver\ResolverByModuleContextMap;
use Nnx\EntryNameResolver\Exception\RuntimeException;
use Nnx\EntryNameResolver\PhpUnit\TestData\ContextResolver\Custom\Service\Module1\InvalidResolverByModuleContextMap;


/**
 * Class ResolverByModuleContextMapFunctionalTest
 *
 * @package Nnx\EntryNameResolver\PhpUnit\Test
 */
class ResolverByModuleContextMapFunctionalTest extends AbstractHttpControllerTestCase
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
                CustomService\Service\CustomServiceComponent::class,
                [
                    CustomService\Service\CustomServiceComponent::class => [
                        CustomService\Module1\Module::MODULE_NAME => CustomService\Module1\CustomServiceComponentModule1::class,
                        CustomService\Module2\Module::MODULE_NAME => CustomService\Module2\CustomServiceComponentModule2::class,
                        CustomService\Module3\Module::MODULE_NAME => CustomService\Module3\CustomServiceComponentModule3::class
                    ]
                ],
                CustomService\Module3\Module::class,
                CustomService\Module3\CustomServiceComponentModule3::class
            ],
            [
                CustomService\Service\CustomServiceComponent::class,
                [
                    CustomService\Service\CustomServiceComponent::class => [
                        CustomService\Module1\Module::MODULE_NAME => CustomService\Module1\CustomServiceComponentModule1::class,
                        CustomService\Module2\Module::MODULE_NAME => CustomService\Module2\CustomServiceComponentModule2::class,
                        CustomService\Module3\Module::MODULE_NAME => CustomService\Module3\CustomServiceComponentModule3::class
                    ]
                ],
                new CustomService\Module3\Module(),
                CustomService\Module3\CustomServiceComponentModule3::class
            ]
        ];
    }


    /**
     * Тестирование резолвинга на основе конфига
     *
     * @dataProvider dataResolveEntryNameByContext
     *
     * @param       $entryName
     * @param array $map
     * @param       $context
     *
     * @param       $resolvedEntryName
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testResolveEntryNameByContext($entryName, array $map, $context, $resolvedEntryName)
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var ResolverByModuleContextMap $resolverByModuleContextMap */
        $resolverByModuleContextMap = $entryNameResolverManager->get(ResolverByModuleContextMap::class, [
            'contextMap' => $map
        ]);

        $actualResolvedEntryName = $resolverByModuleContextMap->resolveEntryNameByContext($entryName, $context);

        static::assertEquals($resolvedEntryName, $actualResolvedEntryName);
    }


    /**
     * Проверка ситуации когда передан не корректный контекст
     *
     * @expectedExceptionMessage Context of type boolean is invalid; Context not string.
     * @expectedException \Nnx\EntryNameResolver\Exception\InvalidContextException
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testResolveEntryNameByInvalidContext()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        /** @var ResolverByModuleContextMap $resolverByModuleContextMap */
        $resolverByModuleContextMap = $entryNameResolverManager->get(ResolverByModuleContextMap::class);

        $resolverByModuleContextMap->resolveEntryNameByContext('test', false);
    }

    /**
     * Проверка ситуации когда,
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testInvalidEntryNameResolverChainClassName()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToContextResolverAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $e = null;
        try {
            $entryNameResolverManager->get(ResolverByModuleContextMap::class, [
                'className' => InvalidResolverByModuleContextMap::class
            ]);
        } catch (\Exception $ex) {
            $e = $ex;
        }

        static::assertInstanceOf(\Exception::class, $e);
        $prevException = $e->getPrevious();
        static::assertInstanceOf(RuntimeException::class, $prevException);
        static::assertEquals('ResolverByModuleContextMap not implements: Nnx\EntryNameResolver\ResolverByModuleContextMap', $prevException->getMessage());
    }
}
