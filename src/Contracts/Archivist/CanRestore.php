<?php

namespace Russsiq\Assistant\Contracts\Archivist;

interface CanRestore
{
    /**
     * Задать рабочий файл резервной копии.
     * 
     * @param  string  $filename
     * @return self
     */
    public function from(string $filename): self;

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * 
     * @return mixed
     */
    public function restore();
}
