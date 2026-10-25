# bridge-form-model — Elastic integration

All references: `src/BridgeFormModel.php` unless stated.

## Discovery (`buildProperties()`, `:793-838`)

When the inited model implements `Blackcube\ActiveRecord\Elastic\ElasticInterface` and `getSchema()` is not `null`, each schema property becomes a bridge:

| Case | Result |
|---|---|
| a real bridge with that name exists and is not Elastic | `LogicException('Elastic property <name> conflicts with existing property in form model.')` |
| no bridge with that name | a new `Bridge` with a form-side endpoint (`property: <name>`, `isElastic: true`, type and format from the schema) and the meta from `prepareMeta()` |
| in every case | a model-side Elastic endpoint, plus the rules of `JsonSchemaRuleMapper::map($attribute, $property, $required)` (`blackcube/active-record`) appended to the bridge rules |

The virtual values live in the private `$elasticAttributes` array (`:63-64`), not in declared properties.

## Access

| Entry point | Behaviour (`:463-514`, `:333-353`) |
|---|---|
| `$form->color`, `$form->color = 'red'`, `isset($form->color)` | `__get` / `__set` / `__isset` on Elastic names only; `__get` of a non-Elastic unknown name returns `null`, `__set` ignores it |
| `$form->getColor()`, `$form->isColor()`, `$form->setColor('red')` | `__call` on Elastic names; anything else goes to `FormModel::__call` when it exists |
| `hasProperty('color')`, `getPropertyValue('color')` | Elastic first, then `FormModel` |
| `getPropertyLabel()`, `getPropertyHint()`, `getPropertyPlaceholder()` | schema `title`, `description`, `placeholder` for an Elastic name, else `FormModel` (`:412-461`) |
| `getElasticPropertyLabels()`, `getElasticPropertyHints()`, `getElasticPropertyPlaceholders()` | the same, for every Elastic name at once (`:360-410`) |

Scenario markers `ALL_ELASTIC_ATTRIBUTES` / `NO_ELASTIC_ATTRIBUTES` include or exclude these names (see `formmodel.md`).

## Meta for widgets (`prepareMeta()`, `:516-582`)

`Bridge::getMeta()` of an Elastic bridge carries `field`, `label` (`title`), `hint` (`description`), `placeholder`, and the widget kind derived from the schema:

| Schema | `field` |
|---|---|
| `type: string` with `format` `file` / `files` | the format, plus `fileType`, `imageWidth`, `imageHeight` when present |
| `type: string` with `format` `wysiwyg`, `textarea`, `email`, `date`, `checkbox`, `radio` | the format |
| `type: string` with `format` `radiolist`, `radioList`, `dropdownlist`, `dropdownList` | the format |
| `type: string` with `format: date-time` | `datetime-local` |
| `type: number` or `integer` | `number` |
| `type: boolean` | `checkbox` |
| anything else | the schema `field` property, else `text` |

`options` (a schema object) is copied as an array; `items` (iterable) becomes a list of `['title', 'value', 'description']`.

## Hydration (`src/ElasticHydrator.php`)

`ElasticHydrator` decorates a `HydratorInterface` (default `new Hydrator()`): `hydrate()` first assigns every resolved Elastic value through `$object->{$name} = …` (the `__set` above), then delegates for the real properties (`:36-57`); `create()` delegates first, then assigns the Elastic values (`:59-82`). It is the outer hydrator of the form hydrator singleton: no setup in the application.

## Transfer

The Elastic endpoints are plain property endpoints on both sides (`property: <name>`): `Mapper` reads `$form->{$name}` and writes `$model->{$name}`, which the model's `ElasticTrait` stores in its JSON column. The schema type (`string`, `number`, `integer`, `boolean`) is the endpoint type: `number` and `integer` are not cast by `Mapper` (its casts cover `int`, `float`, `string`, `bool` only), the value goes through as hydrated.
