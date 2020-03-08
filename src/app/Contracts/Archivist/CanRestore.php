<?php

namespace Russsiq\Assistant\Contracts\Archivist;

interface CanRestore
{
    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @param  string  $filename
     * @param  array  $options
     * @return mixed
     */
    public function restore(string $filename, array $options = []);
}
