<?php

namespace Russsiq\Assistant\Contracts;

interface ArchivistContract
{
    /**
     * Создать резервную копию.
     *
     * @return void
     */
    public function backup();

    /**
     * Восстановить резервную копию.
     *
     * @return void
     */
    public function restore();
}
