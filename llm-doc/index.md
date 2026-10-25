# blackcube/bridge-form-model — LLM index

**Role:** a `yiisoft/form-model` `FormModel` that declares, with `#[Bridge]` attributes, which of its properties or accessors transfer to and from another object (usually an ActiveRecord), with scenario filtering, date and enum conversion, and automatic virtual properties for the Elastic (JSON Schema) attributes of a `blackcube/active-record` model. Nothing transfers without a `#[Bridge]`.

## Package card

| | |
|---|---|
| Namespace | `Blackcube\BridgeFormModel` |
| Form model | `BridgeFormModel` (abstract, extends `Yiisoft\FormModel\FormModel`, implements `RulesProviderInterface`, `DataSetInterface`) |
| Attributes | `Attributes\Bridge` (property or method), `Attributes\IntOrNull` (hydrator parameter attribute) |
| Transfer | `Components\Bridge` (endpoints of one bridged name per class), `Mappers\Mapper` (one transfer with conversion) |
| Elastic | `ElasticHydrator` (hydrates the virtual properties before `Yiisoft\Hydrator\Hydrator`) |
| Extraction | `Interfaces\ExtractDataInterface`, `Traits\ExtractDataTrait` |
| Requires | PHP ^8.2, `ext-intl`, `blackcube/active-record` ^1.0, `blackcube/injector` ^1.0, `yiisoft/active-record` ^1.1, `yiisoft/db` ^2.1, `yiisoft/form-model` ^1.1, `yiisoft/hydrator` ^1.6 |
| Yii config | No config-plugin. The application provides `Yiisoft\Validator\ValidatorInterface` in the container and initializes `Injector`. |
| License | BSD-3-Clause |

## Rules

- A property or accessor transfers only with `#[Bridge]`; the bridge is resolved against the target the first time a model of that class is given (`createFromModel()`, `initFromModel()`, `populateModel()`), once per target class (`src/BridgeFormModel.php:286-296`).
- Direction and filtering differ: model → form (`createFromModel`, `initFromModel`) transfers every bridge; form → model (`populateModel`) transfers only the fields of the active scenario (`:94-100`, `:298-331`).
- `load()` hydrates only the fields that both belong to the active scenario and carry a validation rule: `getRules()` filters `rules()` by scenario (`:214-236`) and `FormHydrator` builds its map from those rules (`vendor/yiisoft/form-model/src/FormHydrator.php:263-298`). A bridged field without a rule keeps its default after `load()`, yet `populateModel()` still transfers it.
- A transfer that fails (`LogicException`, `RuntimeException`, `TypeError`) is swallowed per bridge, silently (`:324-330`): a wrong date string or a type mismatch leaves the target untouched.
- `scenarios()` defaults to one `default` scenario holding every bridged name (`:136-141`); `ALL_ELASTIC_ATTRIBUTES` / `NO_ELASTIC_ATTRIBUTES` expand or strip the Elastic names (`:157-188`).
- Elastic virtual properties exist only after a model implementing `ElasticInterface` with a schema has been given; a real bridged property with the name of a schema property throws `LogicException('Elastic property … conflicts with existing property in form model.')` (`:793-838`).
- The form hydrator is a static singleton resolved through `Injector::get()` (`:72-92`): `Injector::init($container)` is a prerequisite of `load()` and `validate()`.

## Parts

| File | Content | Read when |
|---|---|---|
| `bridge.md` | `#[Bridge]` parameters, resolution of the target accessor for properties and for methods, types and nullability, `Components\Bridge` endpoints, `Mapper` conversions, `IntOrNull`. | before declaring a bridge |
| `formmodel.md` | Lifecycle: scenarios and active fields, `load()` / `loadMultiple()`, `validate()` / `getRules()`, the three transfers, `getData()`, `extractData()`. | before writing or driving a form |
| `elastic.md` | Discovery from the schema, virtual property access, labels / hints / placeholders, the `field` meta mapping for widgets, rules from the schema, `ElasticHydrator`. | before a form on an Elastic model |
| `integration.md` | What the application provides (container, `Injector`), handler flow, no config-plugin, tests. | before plugging the package in |
| `extension.md` | Overridable methods, explicit accessors, `type` override; what does not extend. | before extending |
