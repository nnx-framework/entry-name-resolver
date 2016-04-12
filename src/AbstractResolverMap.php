<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

/**
 * Class AbstractResolverMap
 *
 * @package Nnx\EntryNameResolver
 */
abstract class AbstractResolverMap implements EntryNameResolverInterface
{
    /**
     * Карта используемая для определения имени сервиса в зависимости от контекста вызова
     *
     * 'имяСервиса' => [
     *      'contextKey' => 'имяСервисаДляЭтогоМодуля'
     * ]
     *
     * @var array
     */
    protected $contextMap = [];

    /**
     * @inheritdoc
     *
     * @param      $entryName
     * @param null $context
     *
     * @return null|string
     */
    public function resolveEntryNameByContext($entryName, $context = null)
    {
        if (null === $context) {
            return null;
        }
        $contextKey = $this->buildContextKey($context);

        $map = $this->getContextMap();
        $resolvedEntryName = null;
        if (array_key_exists($entryName, $map) && is_array($map[$entryName]) && array_key_exists($contextKey, $map[$entryName])) {
            $resolvedEntryName = $map[$entryName][$contextKey];
        }

        return $resolvedEntryName;
    }

    /**
     * Преобразует контекст в ключ
     *
     * @param $context
     *
     * @return mixed
     */
    abstract public function buildContextKey($context);

    /**
     * Возвращает карту используемую для определения имени сервиса в зависимости от контекста вызова
     *
     * @return array
     */
    public function getContextMap()
    {
        return $this->contextMap;
    }

    /**
     * Устанавливает карту используемую для определения имени сервиса в зависимости от контекста вызова
     *
     * @param array $contextMap
     *
     * @return $this
     */
    public function setContextMap($contextMap)
    {
        $this->contextMap = $contextMap;

        return $this;
    }
}
