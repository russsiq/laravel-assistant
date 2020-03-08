<?php

namespace Russsiq\Assistant\Contracts\Archivist;

interface CanBackup
{
    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @param  array  $options
     * @return mixed
     */
    public function backup(array $options = []);
}
