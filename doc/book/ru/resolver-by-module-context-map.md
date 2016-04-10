# ResolverByModuleContextMap

Алгоритм  resolver (\Nnx\EntryNameResolver\ResolverByModuleContextMap)следующий:
При создание указать карту описывающую как определить имя службы, в зависимости от того, из какого модуля происходит
попытка получить ее имя.
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

Для установки карты имен служба, необходимо вторым аргументом передать массив с настройками. 

В **настройках должна быть указана секция 'contextMap', которая обазательно должна быть массивом**.

Структура contextMap, следующая:

- Ключем является имя сулжбы(сервиса)
- Значением массив, описывающий какое в итоге нужно использовать имя службы, в зависимости от того из какого модуля произошел вызов
    - Имя модуля
    - Итоговое имя службы
    
В качестве $context'a можно использовать либо имя любого класса, входящий в модуль из которого происходит вызво, либо
объект от такого класса.

