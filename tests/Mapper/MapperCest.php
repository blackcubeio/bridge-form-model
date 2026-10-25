<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeFormModel\Tests\Mapper;

use Blackcube\BridgeFormModel\Components\Bridge;
use Blackcube\BridgeFormModel\Mappers\Mapper;
use Blackcube\BridgeFormModel\Tests\Support\MapperTester;
use DateTimeImmutable;

/**
 * Simple test model with DateTimeImmutable
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class DateTimeSource
{
    public ?DateTimeImmutable $date = null;
}

/**
 * Simple test model with string date
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class StringDateTarget
{
    public ?string $date = null;
}

class MapperCest
{

    public function testDateTimeImmutableToString(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            format: 'Y-m-d',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            format: 'Y-m-d',
            isNullable: true,
        );

        $source = new DateTimeSource();
        $source->date = new DateTimeImmutable('2024-06-15');

        $target = new StringDateTarget();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertEquals('2024-06-15', $target->date);
    }

    public function testDateTimeImmutableNullToString(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            format: 'Y-m-d',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            format: 'Y-m-d',
            isNullable: true,
        );

        $source = new DateTimeSource();
        $source->date = null;

        $target = new StringDateTarget();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertNull($target->date);
    }


    public function testStringToDateTimeImmutable(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            format: 'Y-m-d',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            format: 'Y-m-d',
            isNullable: true,
        );

        $source = new StringDateTarget();
        $source->date = '2024-12-25';

        $target = new DateTimeSource();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertInstanceOf(DateTimeImmutable::class, $target->date);
        $I->assertEquals('2024-12-25', $target->date->format('Y-m-d'));
    }

    public function testStringNullToDateTimeImmutable(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            format: 'Y-m-d',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            format: 'Y-m-d',
            isNullable: true,
        );

        $source = new StringDateTarget();
        $source->date = null;

        $target = new DateTimeSource();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertNull($target->date);
    }

    public function testEmptyStringToDateTimeImmutable(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            format: 'Y-m-d',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            format: 'Y-m-d',
            isNullable: true,
        );

        $source = new StringDateTarget();
        $source->date = '';

        $target = new DateTimeSource();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertNull($target->date);
    }


    public function testDateTimeImmutableWithDefaultFormat(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: DateTimeSource::class,
            property: 'date',
            type: 'DateTimeImmutable',
            isNullable: true,
        );
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            isNullable: true,
        );

        $source = new DateTimeSource();
        $source->date = new DateTimeImmutable('2024-06-15 14:30:00');

        $target = new StringDateTarget();

        $mapper = new Mapper($bridge, $source, $target);
        $mapper->transfer();

        $I->assertEquals('2024-06-15 14:30:00', $target->date);
    }


    public function testSimpleTransferNoConversion(MapperTester $I): void
    {
        $bridge = new Bridge('date');
        $bridge->setEndpoint(
            className: StringDateTarget::class,
            property: 'date',
            type: 'string',
            isNullable: true,
        );

        $secondTarget = new class {
            public ?string $date = null;
        };

        $bridge->setEndpoint(
            className: get_class($secondTarget),
            property: 'date',
            type: 'string',
            isNullable: true,
        );

        $source = new StringDateTarget();
        $source->date = 'simple-string';

        $mapper = new Mapper($bridge, $source, $secondTarget);
        $mapper->transfer();

        $I->assertEquals('simple-string', $secondTarget->date);
    }
}
