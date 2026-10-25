# bridge-form-model — Integration

## Split

- Package: the abstract form model, the attributes, the transfer components, the Elastic hydrator, the extraction interface and trait.
- Application: the concrete form classes, the models they bridge to, a container providing `Yiisoft\Validator\ValidatorInterface`, `Blackcube\Injector\Injector::init($container)` before the first `load()` / `validate()`, the handler flow around them.

## Container

No config-plugin and no DI definition in the package. The form hydrator singleton is resolved through `Injector::get(['class' => FormHydrator::class, '__construct()' => ['hydrator' => new ElasticHydrator(…)]])` (`src/BridgeFormModel.php:72-92`): the container builds `Yiisoft\FormModel\FormHydrator` and autowires its `validator` argument, so `ValidatorInterface` must be defined there (the `yiisoft/validator` config-plugin does it in a Yii3 application).

## Handler flow

```php
// create
$content = new Content();
$form = new ContentForm();
$form->setScenario('create');

// edit
$content = Content::query()->andWhere(['id' => $id])->one();
$form = ContentForm::createFromModel($content);
$form->setScenario('edit');

// process
if ($form->load($request->getParsedBody()) === true && $form->validate() === true) {
    $form->populateModel($content);
    $content->save();
}
```

A GET form (search, filter) loads without scope: `$form->load($request->getQueryParams(), '')`. A form without a model is still a `BridgeFormModel` with `scenarios()` and `rules()` and no `#[Bridge]`: it validates and hydrates, it transfers nothing.

## Form requirements

- `scenarios()` listing the hydratable fields per scenario and `rules()` for each field that must hydrate: without a rule, `load()` skips the field (`formmodel.md`).
- `#[Bridge]` on every property or accessor that transfers; the model stays plain.
- Dates: `#[Bridge(format: 'Y-m-d')]` on the string side of a `DateTimeImmutable` model property.
- Elastic model: nothing to declare, the schema properties appear after the first `initFromModel()` / `createFromModel()`; use `ALL_ELASTIC_ATTRIBUTES` in the scenarios that edit them.

## Tests

Codeception suites `Bridge`, `BridgeFormModel`, `Mapper` (no database) and `Elastic`, `Integration` (MySQL through `tests/Support/MysqlHelper.php`, `DatabaseCestTrait`, migration `tests/Support/Migrations/M000000000000CreateTestContents.php`); `vendor/bin/codecept run` runs them all.
