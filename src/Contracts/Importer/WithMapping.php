<?php

namespace Tec\DataSynchronize\Contracts\Importer;

interface WithMapping
{
    public function map(mixed $row): array;
}
