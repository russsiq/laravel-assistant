<?php

namespace Russsiq\Assistant\Contracts\Archivist;

interface CanBackup
{
    /**
     * Задать рабочий файл резервной копии.
     * 
     * @param  string  $filename
     * @return self
     */
    public function to(string $filename): self;

    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * 
     * @return mixed
     */
    public function backup();
}
