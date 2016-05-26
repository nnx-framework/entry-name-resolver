# Менеджер для получения Resolver'ов

Модуль предоставляет менеджер плагинов для работы с resolver'ами. Данный менеджер зарегистрирован в ServiceLocator приложения
по имени \Nnx\EntryNameResolver\EntryNameResolverManagerInterface.

Для добавления новых resolver'ов можно воспользоваться секцией nnx_entry_name_resolverв конфиге приложения, либо реализовав у модуля интерфейс \Nnx\EntryNameResolver\EntryNameResolverProviderInterface.

Пример получения менеджера:

```php

/** @var EntryNameResolverManager $entryNameResolverManager */
$entryNameResolverManager = $this->getServiceLocator()->get(EntryNameResolverManagerInterface::class);

```

По умолчанию каждый раз создается новый экземпляр resolver'а.
