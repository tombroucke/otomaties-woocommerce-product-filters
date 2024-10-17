<?php

namespace Otomaties\ProductFilters\Filters\Contracts;

interface Filter
{
    public function modifyQueryArgs(array $queryArgs, mixed $value): array;
}
