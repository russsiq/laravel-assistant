<?php

namespace Russsiq\Assistant\Contracts\Archivist;

/**
 * Контракт публичных методов Распаковщика.
 * @var interface
 */
interface CanRestore
{
    /**
     * Задать рабочий файл резервной копии.
     * @param  string  $filename
     * @return self
     */
    public function from(string $filename): self;

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @return mixed
     */
    public function restore();
}
