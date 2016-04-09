<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

use Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface;

/**
 * Class EntryNameResolver
 *
 * @package Nnx\EntryNameResolver\EntryNameResolver
 */
class ResolverByClassName implements EntryNameResolverInterface
{
    /**
     * Менеджер настроек модулей
     *
     * @var \Nnx\ModuleOptions\ModuleOptionsPluginManagerInterface
     */
    protected $moduleOptionsPluginManager;

    /**
     * Паттерн по которому из имени интерфейса можно получить строку, являющеюся заготовкой для формирования имени класса
     *
     * @var string
     */
    protected $entryBodyNamePattern = '/(.+)?Interface$/';

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
     * @param      $entryName
     * @param null $context
     *
     * @return null|string
     * @throws \Nnx\EntryNameResolver\Exception\InvalidContextException
     */
    public function resolveEntryNameByContext($entryName, $context = null)
    {
        if (!interface_exists($entryName) && !class_exists($entryName)) {
            return null;
        }

        if (null === $context) {
            return $entryName;
        }

        $contextClass = $this->buildClassNameContext($context);
        $className = $this->buildClassNameByEntryName($entryName);

        $resolveByContext = $this->resolve($className, $contextClass);
        if (null !== $resolveByContext) {
            return $resolveByContext;
        }

        return $this->resolve($className, $className);
    }

    /**
     * Резолвинг имени класса
     *
     *
     * @param $className
     * @param $contextClass
     *
     * @return null|string
     */
    protected function resolve($className, $contextClass)
    {
        $moduleOptionsPluginManager = $this->getModuleOptionsPluginManager();
        if (!$moduleOptionsPluginManager->hasModuleNameByClassName($className)) {
            return null;
        }
        $classModuleName = $moduleOptionsPluginManager->getModuleNameByClassName($className);

        $shortClassName = substr($className, strlen($classModuleName));

        if (!$moduleOptionsPluginManager->hasModuleNameByClassName($contextClass)) {
            return null;
        }
        $contextModuleName = $moduleOptionsPluginManager->getModuleNameByClassName($contextClass);

        $resolvedClassName = $contextModuleName . $shortClassName;

        if (!class_exists($resolvedClassName)) {
            return null;
        }

        return $resolvedClassName;
    }

    /**
     * Получает имя класса контекста
     *
     * @param $context
     *
     * @throws Exception\InvalidContextException
     */
    public function buildClassNameContext($context)
    {
        $contextClass = $context;
        if (is_object($contextClass)) {
            $contextClass = get_class($contextClass);
        }

        if (!is_string($contextClass)) {
            $errMsg = sprintf(
                'Context of type %s is invalid; Context not string.',
                (is_object($context) ? get_class($context) : gettype($context))
            );
            throw new Exception\InvalidContextException($errMsg);
        }

        return $contextClass;
    }



    /**
     * Получение именки класса, на основе имени интерфейса
     *
     * @param $entryName
     */
    protected function buildClassNameByEntryName($entryName)
    {
        if (!interface_exists($entryName)) {
            return $entryName;
        }

        $interfaceParts = explode('\\', $entryName);
        $originalShortName = array_pop($interfaceParts);

        $entityBodyNameOutput = [];
        $entityBodyNamePattern = $this->getEntryBodyNamePattern();
        preg_match($entityBodyNamePattern, $originalShortName, $entityBodyNameOutput);

        $entityBody = $entryName;
        if (2 === count($entityBodyNameOutput)) {
            $entityBody = $entityBodyNameOutput[1];
        }
        $interfaceParts[] = $entityBody;


        return implode('\\', $interfaceParts);
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

    /**
     * Возвращает паттерн по которому из имени интерфейса можно получить строку, являющеюся заготовкой для формирования имени класса
     *
     * @return string
     */
    public function getEntryBodyNamePattern()
    {
        return $this->entryBodyNamePattern;
    }

    /**
     * Устанавливает паттерн по которому из имени интерфейса можно получить строку, являющеюся заготовкой для формирования имени класса
     *
     * @param string $entryBodyNamePattern
     *
     * @return $this
     */
    public function setEntryBodyNamePattern($entryBodyNamePattern)
    {
        $this->entryBodyNamePattern = $entryBodyNamePattern;

        return $this;
    }
}
