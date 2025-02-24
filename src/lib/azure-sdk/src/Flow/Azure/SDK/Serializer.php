<?php

declare(strict_types=1);

namespace Flow\Azure\SDK;

interface Serializer
{
    public function serialize(mixed $data) : string;
}
