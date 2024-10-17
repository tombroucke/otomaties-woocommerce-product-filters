<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Str;

abstract class Filter
{
    private string $type;

    private string $title;

    private string $component;

    public function __construct(protected string $slug, array $params)
    {
        $this->title = $params['title'];
        $this->type = $params['type'];
        $this->component = $params['component'];
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function value(): mixed
    {
        return null;
    }

    public function componentName()
    {
        return Str::kebab($this->component);
    }
}
