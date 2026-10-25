# bridge-form-model — Extension

## Extension points

| Point | Where | Use |
|---|---|---|
| Scenarios and rules | `scenarios()`, `rules()` (public, meant to be overridden) | every concrete form |
| Target accessors | `#[Bridge(getter:, setter:, property:)]` | names that differ between form and model, `property: false` to make a bridge read-only or write-only on the model |
| Types | `#[Bridge(type:)]` | untyped or `mixed` ends, forced `DateTimeImmutable` |
| Transfer filter | `protected transfer(object $from, object $to, bool $filter = true)` | log or rethrow the swallowed exceptions, add a post-transfer hook |
| Active fields | `protected getActiveFields()`, `protected getElasticAttributeNames()` | other scenario semantics |
| Labels, hints, placeholders | `getPropertyLabel()`, `getPropertyHint()`, `getPropertyPlaceholder()` (public, from `FormModel`) | translations, non-schema sources |
| Extraction | `ExtractDataInterface` + `ExtractDataTrait`, or your own `extractData()` | other array shapes |
| Hydration casts | `#[IntOrNull]` or any `yiisoft/hydrator` parameter attribute on a form property | per-field casting beyond the type casters |
| Several models | `initFromModel()` called with models of different classes | one form bridging two models; same-named bridges share one `Bridge` with one endpoint per class |

## Not extensible

| Element | Reason |
|---|---|
| Hydrator stack (type casters, `ElasticHydrator`) | `private static formHydrator()` with the stack hardcoded (`src/BridgeFormModel.php:72-92`); one singleton per process |
| Bridge resolution order (`set` → property, `get` → `is` → property) | `private buildProperties()` |
| Elastic conflict rule, meta mapping (`prepareMeta()`) | `private` methods (`:516-582`, `:584-841`) |
| `Mapper` conversions | the `Mapper` is instantiated inside `transfer()` (`:326`): another mapper means overriding `transfer()` entirely |
| Endpoint merge semantics | `Components\Bridge::setEndpoint()` keeps non-null values, `isNullable` / `isElastic` only rise to `true` (`src/Components/Bridge.php:33-73`) |
| Scenario cache | `private array $activeScenarioFields`, reset only by `ensureInited()` of a new class |
| Scenario markers | `const ALL_ELASTIC_ATTRIBUTES`, `NO_ELASTIC_ATTRIBUTES`, filtered by name in `getActiveFields()` |
