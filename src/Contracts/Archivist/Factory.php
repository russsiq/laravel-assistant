<?php

namespace Russsiq\Assistant\Contracts\Archivist;

use Russsiq\Assistant\Contracts\ArchivistContract;

interface Factory
{
    /**
     * Получить экземпляр класса оператора.
     * 
     * @param  string|null  $name
     * @return ArchivistContract
     *
     * @throws \InvalidArgumentException
     */
    public function operator(string $name = null): ArchivistContract;
}
