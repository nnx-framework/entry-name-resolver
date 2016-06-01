# ResolverByModuleContextMap

Алгоритм  resolver (\Nnx\EntryNameResolver\ResolverByModuleContextMap) следующий:
При создании указать карту, описывающую, как определить имя службы в зависимости от того, из какого модуля происходит попытка получить ее имя.

Через опции можно передать следующие параметры:

Имя параметра|Обязательный|Тип   |Описание
-------------|------------|------|---------
contextMap   |нет         |массив|Карта, описывающая, как определить имя службы в зависимости от того, из какого модуля происходит попытка получить ее имя
className    |нет         |строка|По умолчанию - \Nnx\EntryNameResolver\EntryNameResolverChain, можно задать свое значение. Важно, чтобы указанный класс был потомком \Nnx\EntryNameResolver\EntryNameResolverChain 

## Указание карты служб через конфиг

Пример добавления конфига:

```php

/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getAServiceLocator()->get(EntryNameResolverManagerInterface::class);

/** @var ResolverByModuleContextMap $resolverByModuleContextMap */
$resolverByModuleContextMap = $entryNameResolverManager->get(ResolverByModuleContextMap::class, [
    'contextMap' => [
        CustomService\Service\CustomServiceComponent::class => [
            CustomService\Module1\Module::MODULE_NAME => CustomService\Module1\CustomServiceComponentModule1::class,
            CustomService\Module2\Module::MODULE_NAME => CustomService\Module2\CustomServiceComponentModule2::class,
            CustomService\Module3\Module::MODULE_NAME => CustomService\Module3\CustomServiceComponentModule3::class
        ]
    ]
]);

$resolverByModuleContextMap->resolveEntryNameByContext($entryName, $context);
```

Для установки карты имен служб необходимо вторым аргументом передать массив с настройками. 

В настройках должна быть указана секция 'contextMap', которая обазательно должна быть массивом.

Структура contextMap следующая:

- Ключом является имя службы (сервиса);
- Значением — массив, описывающий, какое в итоге нужно использовать имя службы в зависимости от того, из какого модуля произошел вызов;
    - Имя модуля;
    - Итоговое имя службы.
    
В качестве $context'a можно использовать либо имя любого класса, входящего в модуль, из которого происходит вызов, либо объект от такого класса.

## Создание с помощью стандартной фабрики своей реализации ResolverByModuleContextMap

В случае если необходимо с помощью стандартной фабрики настроить собственную реализацию ResolverByModuleContextMap, то с помощью настройкии className можно указать имя класса, который является наследником \Nnx\EntryNameResolver\ResolverByModuleContextMap.

```php

/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getAServiceLocator()->get(EntryNameResolverManagerInterface::class);

/** @var ResolverByModuleContextMap $resolverByModuleContextMap */
$resolverByModuleContextMap = $entryNameResolverManager->get(ResolverByModuleContextMap::class, [
    'className' => ResolverByModuleContextMap::class,
    'contextMap' => [
        CustomService\Service\CustomServiceComponent::class => [
            CustomService\Module1\Module::MODULE_NAME => CustomService\Module1\CustomServiceComponentModule1::class,
            CustomService\Module2\Module::MODULE_NAME => CustomService\Module2\CustomServiceComponentModule2::class,
            CustomService\Module3\Module::MODULE_NAME => CustomService\Module3\CustomServiceComponentModule3::class
        ]
    ]
]);

$resolverByModuleContextMap->resolveEntryNameByContext($entryName, $context);
```
