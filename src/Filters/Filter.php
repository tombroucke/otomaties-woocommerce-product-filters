<?php

namespace Otomaties\ProductFilters\Filters;

use Illuminate\Support\Str;
use Otomaties\ProductFilters\Filters\Contracts\Filter as FilterInterface;

abstract class Filter implements FilterInterface
{
    private ?string $type;

    private string $title;

    private string $component;

    public function __construct(protected string $slug, array $params)
    {
        $this->title = $params['title'];
        $this->component = $params['component'];
        $this->type = $params['type'] ?? null;
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

    public function component()
    {
        return Str::kebab($this->component);
    }
}
