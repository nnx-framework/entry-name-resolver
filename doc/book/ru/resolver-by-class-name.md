# ResolverByClassName

Автоматическое определение имени службы в зависимости от контекста. Алгоритм работы resolver'a:

- Убедиться, что имя службы — это существующий класс или интерфейс;
- Убедиться, что передан контекст (это или имя класса или объект);
- Подготовить имя класса на основе имени службы. Если в качестве имени службы используется интерфейс, то из его имени удалится слово Interface;
- Проверить, есть ли в модуле, к которому принадлежит контекст, класс, расположенный так же по отношению к корню модуля;
- Если предыдущая проверка не дала результаты, то сделать аналогичную, но контекстом выступит уже имя службы.

Несколько примеров:

Пример - 1:

```txt
project
    vendor
        module1
            src
                Component
            Module
        module2
            src
                ComponentInterface
                Component
            Module
```

```php


/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getServiceLocator()->get(EntryNameResolverManagerInterface::class);

/** @var ResolverByClassName $resolverByClassName */
$resolverByClassName = $entryNameResolverManager->get(ResolverByClassName::class);

$actualResolvedEntryName = $resolverByClassName->resolveEntryNameByContext(ComponentInterface::class, Module1\Module::class);

//$actualResolvedEntryName = Module1\Component

```

Пример - 2:

```txt
project
    vendor
        module1
            src
                FooClass
            Module
        module2
            src
                ComponentInterface
                Component
            Module
```

```php


/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getServiceLocator()->get(EntryNameResolverManagerInterface::class);

/** @var ResolverByClassName $resolverByClassName */
$resolverByClassName = $entryNameResolverManager->get(ResolverByClassName::class);

$actualResolvedEntryName = $resolverByClassName->resolveEntryNameByContext(ComponentInterface::class, Module1\Module::class);

//$actualResolvedEntryName = Module2\Component

```
