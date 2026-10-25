# bridge-form-model — Bridges and conversion

## `#[Bridge]` (`src/Attributes/Bridge.php`)

`#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]`, constructor `(?string $name = null, ?string $type = null, ?string $format = null, string|false|null $property = null, ?string $getter = null, ?string $setter = null)` (`:22-33`).

| Parameter | Effect |
|---|---|
| `name` | bridge name, i.e. the name looked up on the target (`set<Name>`, `get<Name>`, `is<Name>`, `$<name>`); default: the property name, or the accessor name without its `get`/`set`/`is` prefix |
| `type` | forces the type on both ends (`src/BridgeFormModel.php:628`, `:633`); use it when reflection gives none (`mixed`, untyped) |
| `format` | date format for `DateTimeImmutable` ↔ `string`, default `Y-m-d H:i:s` (`src/Mappers/Mapper.php:45`) |
| `property` | explicit target property; `false` disables the property fallback: reads give `null`, writes do nothing (`src/Components/Bridge.php:85-89`, `:104-107`) |
| `getter`, `setter` | explicit target accessors; always win over detection |

## Resolution (`buildProperties()`, `src/BridgeFormModel.php:584-791`)

Only public, non-static properties and methods of the form are scanned. For each one, the target accessor is searched on the given model:

| Declared on the form | Target write side | Target read side |
|---|---|---|
| property `$name` | explicit `setter`, else `set<Name>()`, else (when `property` is `null`) `$name` | explicit `getter`, else `get<Name>()`, else `is<Name>()`, else `$name` |
| method `get<Name>()` / `is<Name>()` | explicit `setter`, else `set<Name>()`, else `$name` | — (the form side reads through the method) |
| method `set<Name>()`, or any other method name | — (the form side writes through the method) | explicit `getter`, else `get<Name>()`, else `is<Name>()`, else `$name` |

A bridge is kept only when at least one target accessor or property was found (`:619`, `:694`, `:749`); otherwise the attribute is ignored. A method whose name has no `get`/`set`/`is` prefix is treated as a setter under its full name (`:676-678`).

Types: the form side takes the property type, the method return type or the first parameter type; the target side takes the getter return type, the setter first parameter type or the property type, in that order (`:633-654`, `:708-724`, `:763-776`). Nullability follows the same source. `type` overrides both.

## `Components\Bridge` (`src/Components/Bridge.php`)

One instance per bridge name, holding one endpoint per class (`setEndpoint(string $className, ?string $getter, ?string $setter, string|false|null $property, ?string $type, ?string $format, bool $isNullable, bool $isElastic)`, `:33-73`). An endpoint update merges only non-null values; `isNullable` and `isElastic` can only become `true`.

| Method | Behaviour |
|---|---|
| `get(mixed $model)` | getter call, else `null` when `property` is `false`, else the property (`property` or the bridge name) (`:75-92`) |
| `set(mixed $model, mixed $value)` | setter call, else the property unless `property` is `false` (`:94-108`) |
| both | `LogicException('Model class not registered: …')` for a class without endpoint |
| `getType()`, `getFormat()`, `isNullable()` (by model), `isElastic(string $className)`, `hasEndpoint()`, `getEndpoint()`, `isTransferable()` (≥ 2 endpoints), `getMeta()` / `setMeta()`, `getRules()` / `setRules()` | accessors |

## `Mapper::transfer()` (`src/Mappers/Mapper.php:33-77`)

Reads through the source endpoint, converts, writes through the target endpoint:

| Source type → target type | Conversion |
|---|---|
| `DateTimeImmutable` → `string` | `format($format)`; `null` or `''` → `null` when the target is nullable, else `RuntimeException('Non nullable date property is null')` |
| `string` → `DateTimeImmutable` | `new DateTimeImmutable($value)`; `null` or `''` handled the same way |
| `BackedEnum` value | replaced by `->value` |
| nullable target, `null` or `''` | `null` |
| scalar → `BackedEnum` target | `$toType::from($value)` |
| non-null → `int`, `float`, `string`, `bool` | PHP cast |
| anything else | as is |

Exceptions thrown here surface in `transfer()` of the form model, which swallows them (`src/BridgeFormModel.php:324-330`).

## `#[IntOrNull]` (`src/Attributes/IntOrNull.php`)

Hydrator parameter attribute (`TARGET_PROPERTY | TARGET_PARAMETER`, repeatable) for `yiisoft/hydrator`: a resolved `''` becomes `null`, any other resolved value becomes `(int)`; an unresolved value is left alone (`:37-54`). For form properties typed `?int` fed by HTML strings.
