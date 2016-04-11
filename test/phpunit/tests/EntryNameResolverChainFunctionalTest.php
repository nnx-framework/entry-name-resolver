<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver\PhpUnit\Test;

use Nnx\EntryNameResolver\PhpUnit\TestData\TestPaths;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Nnx\EntryNameResolver\EntryNameResolverManagerInterface;
use Nnx\EntryNameResolver\EntryNameResolverChain;
use Nnx\EntryNameResolver\Exception\RuntimeException;
use Nnx\EntryNameResolver\EntryNameResolverInterface;
use Nnx\EntryNameResolver\EntryNameResolverManager;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class EntryNameResolverIntegrationTest
 *
 * @package Nnx\EntryNameResolver\PhpUnit\Test
 */
class EntryNameResolverChainFunctionalTest extends AbstractHttpControllerTestCase
{
    /**
     * Проверка получения резолвера с некорректным конфигом.
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testInvalidEntryNameResolverChainConfig()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $e = null;
        try {
            $entryNameResolverManager->get(EntryNameResolverChain::class, [
                'resolvers' => [
                    'invalidEntryNameResolver'
                ]
            ]);
        } catch (\Exception $ex) {
            $e = $ex;
        }

        static::assertInstanceOf(\Exception::class, $e);
        $prevException = $e->getPrevious();
        static::assertInstanceOf(RuntimeException::class, $prevException);
        static::assertEquals('Entry name resolver config is not array', $prevException->getMessage());
    }

    /**
     * Проверка получения резолвера с конфигом, в котором в описание вложенного резолвера не указано имя.
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testNestedResolverInvalidName()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $e = null;
        try {
            $entryNameResolverManager->get(EntryNameResolverChain::class, [
                'resolvers' => [
                    [

                    ]
                ]
            ]);
        } catch (\Exception $ex) {
            $e = $ex;
        }

        static::assertInstanceOf(\Exception::class, $e);
        $prevException = $e->getPrevious();
        static::assertInstanceOf(RuntimeException::class, $prevException);
        static::assertEquals('Resolver entry name not found', $prevException->getMessage());
    }

    /**
     * Проверка получения резолвера с конфигом, в котором в опции вложенного резолвера описаны не строкой
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function testNestedResolverInvalidOptions()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $e = null;
        try {
            $entryNameResolverManager->get(EntryNameResolverChain::class, [
                'resolvers' => [
                    [
                        'name' => 'test',
                        'options' => 'notArray'
                    ]
                ]
            ]);
        } catch (\Exception $ex) {
            $e = $ex;
        }

        static::assertInstanceOf(\Exception::class, $e);
        $prevException = $e->getPrevious();
        static::assertInstanceOf(RuntimeException::class, $prevException);
        static::assertEquals('Resolver options is not array', $prevException->getMessage());
    }

    /**
     * Проверка работы фабрики по созданию цепочки резолверов
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \PHPUnit_Framework_Exception
     * @throws \Zend\ServiceManager\Exception\InvalidServiceNameException
     */
    public function testInsertResolverChain()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        $entryName = 'testEntryName';
        $context = $this;



        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $expectedSequence = [3, 2, 1, 4];

        $actualSequence = [];

        $mockResolver1 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver1->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 1;
            }));
        $mockResolverName1 = spl_object_hash($mockResolver1);
        $entryNameResolverManager->setService($mockResolverName1, $mockResolver1);

        $mockResolver2 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver2->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 2;
            }));
        $mockResolverName2 = spl_object_hash($mockResolver2);
        $entryNameResolverManager->setService($mockResolverName2, $mockResolver2);

        $mockResolver3 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver3->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 3;
            }));
        $mockResolverName3 = spl_object_hash($mockResolver3);
        $entryNameResolverManager->setService($mockResolverName3, $mockResolver3);


        $mockResolver4 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver4->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 4;
            }));
        $mockResolverName4 = spl_object_hash($mockResolver4);
        $entryNameResolverManager->setService($mockResolverName4, $mockResolver4);



        /** @var EntryNameResolverChain $entryNameResolverChain */
        $entryNameResolverChain = $entryNameResolverManager->get(EntryNameResolverChain::class, [
            'resolvers' => [
                [
                    'name' => $mockResolverName1,
                    'priority'  => 70
                ],
                [
                    'name' => $mockResolverName2,
                    'priority'  => 80
                ],
                [
                    'name' => $mockResolverName3,
                    'priority'  => 100
                ],
                [
                    'name' => $mockResolverName4
                ],

            ]
        ]);

        $entryNameResolverChain->resolveEntryNameByContext($entryName, $context);


        static::assertEquals($expectedSequence, $actualSequence);
    }


    /**
     * Проверка получения результатов из цепочки резолверов
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \PHPUnit_Framework_Exception
     * @throws \Zend\ServiceManager\Exception\InvalidServiceNameException
     */
    public function testReturnResult()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        $entryName = 'testEntryName';
        $context = $this;

        $expectedResolveEntryName = 'expectedResolveEntryName';


        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $mockResolver1 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver1->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context));
        $mockResolverName1 = spl_object_hash($mockResolver1);
        $entryNameResolverManager->setService($mockResolverName1, $mockResolver1);

        $mockResolver2 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver2->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context));
        $mockResolverName2 = spl_object_hash($mockResolver2);
        $entryNameResolverManager->setService($mockResolverName2, $mockResolver2);

        $mockResolver3 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver3->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnValue($expectedResolveEntryName));
        $mockResolverName3 = spl_object_hash($mockResolver3);
        $entryNameResolverManager->setService($mockResolverName3, $mockResolver3);


        $mockResolver4 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver4->expects(static::never())
            ->method('resolveEntryNameByContext');
        $mockResolverName4 = spl_object_hash($mockResolver4);
        $entryNameResolverManager->setService($mockResolverName4, $mockResolver4);



        /** @var EntryNameResolverChain $entryNameResolverChain */
        $entryNameResolverChain = $entryNameResolverManager->get(EntryNameResolverChain::class, [
            'resolvers' => [
                [
                    'name' => $mockResolverName1,
                    'priority'  => 80
                ],
                [
                    'name' => $mockResolverName2,
                    'priority'  => 100
                ],
                [
                    'name' => $mockResolverName3,
                    'priority'  => 70
                ],
                [
                    'name' => $mockResolverName4
                ],

            ]
        ]);

        $actualResolveEntryName = $entryNameResolverChain->resolveEntryNameByContext($entryName, $context);

        static::assertEquals($expectedResolveEntryName, $actualResolveEntryName);
    }


    /**
     * Проверка получения результатов из цепочки резолверов
     *
     *
     * @throws \Zend\Stdlib\Exception\LogicException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \PHPUnit_Framework_Exception
     * @throws \Zend\ServiceManager\Exception\InvalidServiceNameException
     */
    public function testPrependResolver()
    {
        /** @noinspection PhpIncludeInspection */
        $this->setApplicationConfig(
            include TestPaths::getPathToDefaultAppConfig()
        );

        $entryName = 'testEntryName';
        $context = $this;



        /** @var EntryNameResolverManager $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $expectedSequence = [1, 3, 2, 4];

        $actualSequence = [];

        $mockResolver2 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver2->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 2;
            }));
        $mockResolverName2 = spl_object_hash($mockResolver2);
        $entryNameResolverManager->setService($mockResolverName2, $mockResolver2);

        $mockResolver3 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver3->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 3;
            }));
        $mockResolverName3 = spl_object_hash($mockResolver3);
        $entryNameResolverManager->setService($mockResolverName3, $mockResolver3);


        $mockResolver4 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver4->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 4;
            }));
        $mockResolverName4 = spl_object_hash($mockResolver4);
        $entryNameResolverManager->setService($mockResolverName4, $mockResolver4);



        /** @var EntryNameResolverChain $entryNameResolverChain */
        $entryNameResolverChain = $entryNameResolverManager->get(EntryNameResolverChain::class, [
            'resolvers' => [
                [
                    'name' => $mockResolverName2,
                    'priority'  => 80
                ],
                [
                    'name' => $mockResolverName3,
                    'priority'  => 100
                ],
                [
                    'name' => $mockResolverName4
                ],

            ]
        ]);


        /** @var PHPUnit_Framework_MockObject_MockObject|EntryNameResolverInterface $mockResolver1 */
        $mockResolver1 = $this->getMock(EntryNameResolverInterface::class);
        $mockResolver1->expects(static::once())
            ->method('resolveEntryNameByContext')
            ->with(static::equalTo($entryName), static::equalTo($context))
            ->will(static::returnCallback(function () use (&$actualSequence) {
                $actualSequence[] = 1;
            }));
        $entryNameResolverChain->prependResolver($mockResolver1);

        $entryNameResolverChain->resolveEntryNameByContext($entryName, $context);


        static::assertEquals($expectedSequence, $actualSequence);
        static::assertCount(4, $entryNameResolverChain);
    }

    /**
     * Проверка ситуации когда, при создание фабрики, указан некорректный класс резолвера
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
            include TestPaths::getPathToDefaultAppConfig()
        );

        /** @var EntryNameResolverManagerInterface $entryNameResolverManager */
        $entryNameResolverManager = $this->getApplicationServiceLocator()->get(EntryNameResolverManagerInterface::class);

        $e = null;
        try {
            $entryNameResolverManager->get(EntryNameResolverChain::class, [
                'className' => \stdClass::class
            ]);
        } catch (\Exception $ex) {
            $e = $ex;
        }

        static::assertInstanceOf(\Exception::class, $e);
        $prevException = $e->getPrevious();
        static::assertInstanceOf(RuntimeException::class, $prevException);
        static::assertEquals('EntryNameResolverChain not implements: Nnx\EntryNameResolver\EntryNameResolverChain', $prevException->getMessage());
    }
}
