# yii2-core
Набор утилит, компонентов, виджетов и базовых классов для создания приложений на Yii2.
Библиотека в первую очередь предназначена для проектов, созданных на основе [extpoint/project-boilerplate]()https://github.com/ExtPoint/project-boilerplate).

Описание возможностей:

- [Types](docs/types.md) - типы данных приложения, описывающие их поведение, формат, способы отображения и ввода;
- [Model](docs/model.md) - базовая модель, дополняющая `ActiveRecord`.

## Обновление на версию 1.4.x

Приложения, базирующиеся на ранних версиях `extpoint/yii2-core` нуждаются в обновлении:

- [Обновите миграции](docs/migration.md)
- Добавьте компонент `Types` в конфигурацию:
```php
    'components' => [
        'types' => [
            'class' => 'extpoint\yii2\components\Types',
        ],
        // ...
    ],
```
- Метод `Module::coreUrlRules()` теперь должен быть `public`