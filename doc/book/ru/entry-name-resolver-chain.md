# EntryNameResolverChain

Позволяет создать цепочку resolver'ов. Когда происходит попытка получить имя службы исходя из контекста, данный resolver
пробегается по всем добавленным в цепочку resolver'ам. В случае **когда resolver из цепочки возвращает отличное от null  
значение, обходи цепочки прекращается, а найденное значение, считается искомым**.

Для EntryNameResolverChain существует фабрика(\Nnx\EntryNameResolver\EntryNameResolverChainFactory) позволяющая задать 
цепочку на основе конфигов.

Пример использования: 

```php

/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getServiceLocator()->get(EntryNameResolverManagerInterface::class);

/** @var EntryNameResolverChain $entryNameResolverChain */
$entryNameResolverChain = $entryNameResolverManager->get(EntryNameResolverChain::class, [
    'resolvers' => [
        [
            'name' => 'resolverName1',
            'options' => [],
            'priority'  => 80
        ],
        [
            'name' => 'resolverName2',
            'priority'  => 100
        ],
        [
            'name' => 'resolverName3'
        ],

    ]
]);

```

При создание экземпляра EntryNameResolverChain с помощью менеджера плагинов EntryNameResolverManagerInterface, необходимо
вторым аргументом передать массив с настройками.

В **настройках должна быть указана секция 'resolvers', которая обазательно должна быть массивом**. Каждый элемент данной
секции, представляет из себя массив описывающий resolver, который должен быть добавлен в цепочку.

Описание структуры конфига resolver'a который должне быть добавлен в цепочку:

Имя параметра|Обязательный|Тип   |Описание
-------------|------------|------|---------
name         |да          |строка|Имя resolver'a по которому его можно получить из EntryNameResolverManager
options      |нет         |массив|Массив с настройками resolver'a
priority     |нет         |число |Приоритет resolver'a в цепочки. Позволяет управлять порядком вызова resolver'ов

