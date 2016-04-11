# EntryNameResolverMirror

Пожалуй самый простой из resolver'ов. EntryNameResolverMirror всегда возвращает тоже самое значение $entryName, что было
подано на вход. Контекст никаким образом не влияет на результат. 

Данный resolver хорошо подходит, когда используется цепочка resolver'ов, и нужно что бы в случае если ни один из resolver'ов
не справился со своей задачей, \Nnx\EntryNameResolver\EntryNameResolverChain вернул бы то же самое значение $entryName, что было
передано ему на вход.