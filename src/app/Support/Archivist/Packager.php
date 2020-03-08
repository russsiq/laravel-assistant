<?php

namespace Russsiq\Assistant\Support\Archivist;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\Archivist\CanBackup;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Упакощика.
 */
class Packager extends AbstractArchivist implements CanBackup
{
    /**
     * Установить массив параметров.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        if (empty($options['backup'])) {
            throw new InvalidArgumentException(
                "Action [backup] is not defined."
            );
        }

        return parent::setOptions($options['backup']);
    }

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute()
    {
        return $this->backup($this->options);
    }

    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @param  array  $options
     * @return void
     */
    public function backup(array $options = [])
    {
        dd('backup after execute', $this->options);
    }
}
