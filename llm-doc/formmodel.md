# bridge-form-model — Form model lifecycle

All references: `src/BridgeFormModel.php`.

## Scenarios

| Element | Behaviour |
|---|---|
| `SCENARIO_DEFAULT = 'default'` | initial scenario (`:47`, `:55`) |
| `scenarios(): array` | `scenario => [field, …]`; default implementation: `['default' => every bridged name]` (`:136-141`); overridden by the form |
| `setScenario(string): static`, `getScenario(): string` | (`:125-134`) |
| `ALL_ELASTIC_ATTRIBUTES`, `NO_ELASTIC_ATTRIBUTES` | markers inside a scenario list: the first appends every Elastic name, the second removes them; both markers are dropped from the result (`getActiveFields()`, `:157-188`) |

`getActiveFields()` is `protected` and cached per scenario; the caches are reset when a new model class is inited (`:293-294`).

## Loading

`load(mixed $data, ?string $scope = null): bool` (`:190-200`): `$scope` defaults to `getFormName()` (the short class name, from `FormModel`); `''` means the data array itself; returns `false` when `$data[$scope]` is not an array. Hydration = `FormHydrator::populate($this, $data, scope: $scope)` with `strict = null`, so the map is the list of properties with rules: the rules found by attributes on the form plus `getRules()` (`vendor/yiisoft/form-model/src/FormHydrator.php:263-298`). Consequences:

- a field hydrates only if it is in the active scenario and has a rule in `rules()` (or an Elastic rule);
- a GET form loads with `load($queryParams, '')`;
- keys outside the map are ignored (`ArrayData` strict).

The hydrator stack (`formHydrator()`, `:72-92`): `ElasticHydrator` around `Yiisoft\Hydrator\Hydrator` with `CompositeTypeCaster(NullTypeCaster(emptyString: true), PhpNativeTypeCaster, NonArrayTypeCaster, HydratorTypeCaster)`, built once per process through `Injector::get()`, the `validator` argument of `FormHydrator` coming from the container.

`static loadMultiple(array $models, array $data, ?string $scope = null): bool` (`:102-123`): `$scope` defaults to the form name of the first model; expects `$data[$scope][$i]` arrays indexed like `$models`; calls `load([$scope => $data[$scope][$i]], $scope)` on each; `true` when at least one model received an array.

## Validation

`validate(): bool` (`:202-207`): `FormHydrator::validate($this)` then `processValidationResult()`. The validator reads the rules from `getRules()` (`RulesProviderInterface`) and the values through `getPropertyValue()` (`DataSetInterface`).

`getRules(): array` (`:214-236`): for each active field, the entry of `rules()` plus the rules of the bridge (Elastic rules from the schema), merged. `rules()` defaults to `[]` (`:209-212`).

## Transfers

| Method | Direction | Filter |
|---|---|---|
| `static createFromModel(object $model): static` (`:94-100`) | model → new form | none: every bridge |
| `initFromModel(object $model): void` (`:298-302`) | model → this form | none |
| `populateModel(object $model): void` (`:304-308`) | this form → model | active scenario fields only |

Each one calls `ensureInited($model)` first: `buildProperties()` runs once per model class, merging into the existing bridges (`:286-296`), so a form can be inited from several models of different classes. `transfer()` (`protected`, `:310-331`) runs one `Mapper` per bridge and swallows `LogicException`, `RuntimeException` and `TypeError`.

## Data set

`getData(): array` (`:243-284`): with bridges built, one entry per bridge name read through the form-side endpoint (getter, Elastic store, or property by reflection); without any bridge built yet, the values of the public `#[Bridge]` `get*` / `is*` methods. `hasProperty()` and `getPropertyValue()` answer for Elastic names first, then defer to `FormModel` (`:333-353`).

## Extraction

`Interfaces\ExtractDataInterface::extractData(): array`, implemented by `Traits\ExtractDataTrait` (`src/Traits/ExtractDataTrait.php:28-36`): the active scenario fields read through `getPropertyValue()`. Applied explicitly on the forms that hand their data over (session, queue); the array reloads with `load($data, '')`, so every extracted field needs a rule to come back.
