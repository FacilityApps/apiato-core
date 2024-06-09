<?php

namespace Apiato\Core\Services;

use Apiato\Core\Contracts\HasResourceKey;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use League\Fractal\Scope;
use Spatie\Fractal\Fractal as SpatieFractal;

/**
 * A wrapper class for Spatie\Fractal\Fractal
 *
 * @see \Spatie\Fractal\Fractal
 */
class Response extends SpatieFractal
{
    public function createData(): Scope
    {
        $this->withResourceName($this->defaultResourceName());
        $this->parseFieldsets($this->getRequestedFieldsets());
        $this->setAvailableIncludesMeta();

        return parent::createData();
    }

    private function setAvailableIncludesMeta(): void
    {
        $this->addMeta([
            'include' => $this->getTransformerAvailableIncludes()
        ]);
    }

    private function getTransformerAvailableIncludes(): array
    {
        if(is_null($this->transformer) || is_callable($this->transformer)) {
            return [];
        }

        if (is_string($this->transformer)) {
            return (new $this->transformer)->getAvailableIncludes();
        }

        return $this->transformer->getAvailableIncludes();
    }

    private function defaultResourceName(): string
    {
        if ($this->data instanceof HasResourceKey) {
            return $this->data->getResourceKey();
        }

        if (!empty($this->data) && 'collection' === $this->determineDataType($this->data)) {
            $firstItem = $this->data->first();
            if ($firstItem instanceof HasResourceKey) {
                return $firstItem->getResourceKey();
            }
        }

        return '';
    }

    private function getRequestedFieldsets(): array
    {
        $fieldSets = [];
        if ($filters = Request::get(Config::get('apiato.requests.params.filter', 'fieldset'))) {
            foreach ($filters as $filter) {
                [$resourceName, $fields] = explode(':', $filter);
                $field = explode(';', $fields);
                $fieldSets[$resourceName] = $field;
            }
        }

        return $fieldSets;
    }
}