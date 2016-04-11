<?php
/**
 * @link    https://github.com/nnx-framework/entry-name-resolver
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace Nnx\EntryNameResolver;

/**
 * Class EntryNameResolverMirror
 *
 * @package Nnx\EntryNameResolver
 */
class EntryNameResolverMirror implements EntryNameResolverInterface
{
    /**
     * @inheritdoc
     *
     * @param      $entryName
     * @param null $context
     *
     * @return mixed
     */
    public function resolveEntryNameByContext($entryName, $context = null)
    {
        return $entryName;
    }
}
