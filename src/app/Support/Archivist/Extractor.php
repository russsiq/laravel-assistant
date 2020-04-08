<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use InvalidArgumentException;

// Базовые расширения PHP.
use SplFileInfo;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Updater;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\ArchivistContract;
use Russsiq\Assistant\Contracts\Archivist\CanRestore;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Распаковщика.
 */
class Extractor extends AbstractArchivist implements CanRestore
{
    /**
     * Полный путь к выбранному файлу резервной копии.
     * @var string
     */
    protected $filename;

    /**
     * Массовое задание параметров архивирования / восстановления.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options): ArchivistContract
    {
        if (empty($options['restore'])) {
            throw new InvalidArgumentException(
                "Action [restore] is not defined."
            );
        }

        if (isset($options['filename'])) {
            $this->from($options['filename']);
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
     * Задать рабочий файл резервной копии.
     * В текущем методе только задаем имя файла.
     * Проверку существования файла оставляем на метод `restore`.
     * @param  string  $filename
     * @return self
     */
    public function from(string $filename): CanRestore
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @return void
     */
    public function restore()
    {
        $filename = $this->storePath($this->filename);

        if ($this->filesystem->isFile($filename)) {
            $this->filename = $filename;
        } else {
            throw new RuntimeException(sprintf(
                "Cannot open zip archive [%s].",
                $filename
            ));
        }

        $destination = $this->storePath('tmp');

        $this->unzipArchive($this->filename, $destination);

        if (in_array('database', $this->options)) {
            \Schema::disableForeignKeyConstraints();

            $contents = $this->filesystem->getRequire(
                $destination.DIRECTORY_SEPARATOR.Archivist::DATABASE_FILENAME
            );

            foreach ($contents as $table => [
                'columns' => $columns,
                'values' => $values
            ]) {
                $insert = [];

                foreach ($values as $row) {
                    $insert[] = array_combine($columns, $row);
                }

                \DB::table($table)
                    ->truncate();

                \DB::table($table)
                    ->insert($insert);
            }

            \Schema::enableForeignKeyConstraints();
        }

        $messages[] = 'Success';

        // Возвращение массива текстовых сообщений о выполненных операциях.
        return $messages;
    }

    /**
     * Извлечь архив с исходниками для последующего обновления.
     * @param  string  $filename
     * @param  string  $destination
     * @return bool
     */
    public function unzipArchive(string $filename, string $destination): bool
    {
        @ini_set('max_execution_time', 120);

        try {
            $ziparchive = $this->ziparchive->open($filename);
            $ziparchive->extractTo($destination);
            $ziparchive->close();

            $ziparchive->ensureSourceInRootDirectory($destination);

            return true;
        } catch (Throwable $e) {
            // $this->filesystem->delete($filename);

            throw $e;
        }
    }
}
