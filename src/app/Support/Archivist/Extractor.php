<?php

namespace Russsiq\Assistant\Support\Archivist;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\Archivist\CanRestore;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Распакощика.
 */
class Extractor extends AbstractArchivist implements CanRestore
{
    protected $filename;

    /**
     * Установить массив параметров.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        if (empty($options['restore'])) {
            throw new InvalidArgumentException(
                "Action [restore] is not defined."
            );
        }

        if (isset($options['filename'])) {
            $this->filename = $options['filename'];
        }

        return parent::setOptions($options['restore']);
    }

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute()
    {
        return $this->restore($this->filename, $this->options);
    }

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @param  string  $filename
     * @param  array  $options
     * @return void
     */
    public function restore(string $filename, array $options = [])
    {
        $filename = $this->storePath($filename);

        if ($this->filesystem->isFile($filename)) {
            $this->filename = $filename;
        }

        $this->unzipArchive($filename, $this->storePath('tmp'));

        dd('restore after execute', $filename, $options);
    }
}
