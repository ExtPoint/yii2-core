# extpoint-yii2
ExtPoint utils for Yii2-based projects

- [Types](docs/types.md)

# Обновление на версию 1.4.x

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
