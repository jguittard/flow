<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Double;

use function Flow\ETL\DSL\{
    bool_entry,
    datetime_entry,
    enum_entry,
    float_entry,
    generate_random_int,
    int_entry,
    json_entry,
    list_entry,
    map_entry,
    row,
    rows,
    str_entry,
    struct_element,
    struct_entry,
    struct_type,
    type_datetime,
    type_float,
    type_int,
    type_list,
    type_map,
    type_string,
    uuid_entry};
use Flow\ETL\Tests\Fixtures\Enum\BackedStringEnum;
use Flow\ETL\{Extractor, FlowContext};
use Ramsey\Uuid\Uuid;

final class FakeExtractor implements Extractor
{
    public function __construct(private readonly int $total)
    {
    }

    /**
     * @param FlowContext $context
     *
     * @return \Generator<int, Rows, mixed, void>
     */
    public function extract(FlowContext $context) : \Generator
    {
        for ($i = 0; $i < $this->total; $i++) {
            $id = $i;

            yield rows(
                row(
                    int_entry('int', $id),
                    float_entry('float', generate_random_int(100, 100000) / 100),
                    bool_entry('bool', false),
                    datetime_entry('datetime', new \DateTimeImmutable('now')),
                    str_entry('null', null),
                    uuid_entry('uuid', new \Flow\ETL\PHP\Value\Uuid(Uuid::uuid4())),
                    json_entry('json', ['id' => $id, 'status' => 'NEW']),
                    list_entry('list', [1, 2, 3], type_list(type_int())),
                    list_entry('list_of_datetimes', [new \DateTimeImmutable(), new \DateTimeImmutable(), new \DateTimeImmutable()], type_list(type_datetime())),
                    map_entry(
                        'map',
                        ['NEW', 'PENDING'],
                        type_map(type_int(), type_string())
                    ),
                    struct_entry(
                        'struct',
                        [
                            'street' => 'street_' . $id,
                            'city' => 'city_' . $id,
                            'zip' => 'zip_' . $id,
                            'country' => 'country_' . $id,
                            'location' => ['lat' => 1.5, 'lon' => 1.5],
                        ],
                        struct_type([
                            struct_element('street', type_string()),
                            struct_element('city', type_string()),
                            struct_element('zip', type_string()),
                            struct_element('country', type_string()),
                            struct_element(
                                'location',
                                struct_type([
                                    struct_element('lat', type_float()),
                                    struct_element('lon', type_float()),
                                ])
                            ),
                        ]),
                    ),
                    enum_entry('enum', BackedStringEnum::three)
                )
            );
        }
    }
}
