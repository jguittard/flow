<?php

declare(strict_types=1);

namespace Flow\ETL\PHP\Type;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\PHP\Type\Logical\{DateTimeType,
    DateType,
    JsonType,
    ListType,
    MapType,
    StructureType,
    TimeType,
    UuidType,
    XMLElementType,
    XMLType};
use Flow\ETL\PHP\Type\Native\{ArrayType, CallableType, EnumType, NullType, ObjectType, ResourceType, ScalarType};

final class TypeFactory
{
    public static function fromArray(array $data) : Type
    {
        if (!\array_key_exists('type', $data)) {
            throw new \InvalidArgumentException("Missing 'type' key in type definition");
        }

        return match ($data['type']) {
            'scalar' => ScalarType::fromArray($data),
            'callable' => CallableType::fromArray($data),
            'array' => ArrayType::fromArray($data),
            'enum' => EnumType::fromArray($data),
            'null' => NullType::fromArray($data),
            'object' => ObjectType::fromArray($data),
            'resource' => ResourceType::fromArray($data),
            'time' => TimeType::fromArray($data),
            'date' => DateType::fromArray($data),
            'datetime' => DateTimeType::fromArray($data),
            'json' => JsonType::fromArray($data),
            'uuid' => UuidType::fromArray($data),
            'list' => ListType::fromArray($data),
            'map' => MapType::fromArray($data),
            'structure' => StructureType::fromArray($data),
            'xml_element' => XMLElementType::fromArray($data),
            'xml' => XMLType::fromArray($data),
            default => throw new InvalidArgumentException("Unknown type '{$data['type']}'"),
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromString(string $name) : Type
    {
        return match (\mb_strtolower($name)) {
            'int','integer' => self::fromArray(['type' => 'scalar', 'scalar_type' => 'integer']),
            'float' => self::fromArray(['type' => 'scalar', 'scalar_type' => 'float']),
            'string' => self::fromArray(['type' => 'scalar', 'scalar_type' => 'string']),
            'bool','boolean' => self::fromArray(['type' => 'scalar', 'scalar_type' => 'boolean']),
            default => self::fromArray(['type' => $name]),
        };
    }
}
