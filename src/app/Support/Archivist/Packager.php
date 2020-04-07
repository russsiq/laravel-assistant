<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use InvalidArgumentException;
use stdClass;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Archivist;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Schema\Builder;

// Сторонние зависимости.
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Russsiq\Assistant\Commands\BackupDatabase;
use Russsiq\Assistant\Contracts\ArchivistContract;
use Russsiq\Assistant\Contracts\Archivist\CanBackup;
use Russsiq\Assistant\Services\Zipper;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Упаковщика.
 */
class Packager extends AbstractArchivist implements CanBackup
{
    /**
     * Полный путь к сохраняемому файлу резервной копии.
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
        return $this->backup();
    }

    /**
     * Задать рабочий файл резервной копии.
     * @param  string  $filename
     * @return self
     */
    public function to(string $filename): CanBackup
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @return void
     */
    public function backup()
    {
        $messages = [];

        if (! $this->filename) {
            $this->to($this->generateBackupFilename());
        }

        $filename = $this->storePath($this->filename);
        // $this->ensureBackupDoesntAlreadyExist($name, $path);

        // Создание архива.
        $ziparchive = $this->ziparchive->create($filename);

        if (in_array('database', $this->options)) {
            // Создание дампа Базы Данных.
            $contents = $this->backupDatabase();

            // Добавление дампа БД в архив.
            $ziparchive->addFromString('database_backup', $contents);

            // Удаление опции БД из списка запланированных.
            $this->without('database');
        }

        // Добавление директорий в архив.
        $this->backupDirectories($ziparchive, $this->options);

        // Добавление корневых файлов в архив.
        $this->backupFiles($ziparchive, $this->allowedFiles());

        // Закрытие архива.
        $ziparchive->close();

        $messages[] = 'Success';

        // Возвращение массива текстовых сообщений о выполненных операциях.
        return $messages;
    }

    protected function backupDatabase(Collection $tables = null)
    {
        // Получаем список таблиц.
        if (is_null($tables)) {
            $connection = Schema::getConnection();
            $grammar = $connection->getQueryGrammar();
            $tablePrefix = $connection->getTablePrefix();

            $tables = collect(Schema::getAllTables())
                ->map(function (stdClass $row) use ($grammar, $tablePrefix) {
                    return head(array_reverse(
                            explode($tablePrefix, head((array) $row), 2)
                        ));
                });
        }

        $tables = $tables->reject(function ($table, $key) {
            return in_array($table, [

            ], true);
        });

        $headers = '';

        // Комментарий.
        $comment = '// ';

        // Разделитель строк.
        $separator = $comment.str_repeat('=', 60).PHP_EOL;

        // `Пустая` строка.
        $empty = $comment.PHP_EOL;

        // Создаем заголовок.
        $headers .= PHP_EOL.PHP_EOL;
        $headers .= $separator;
        $headers .= $comment."Backup file for `".\EnvManager::get('APP_NAME')."`".PHP_EOL;
        $headers .= $separator;

        $headers .= $empty;
        $headers .= $comment."DATE: ".gmdate("Y-m-d H:i:s", time())." GMT".PHP_EOL;
        $headers .= $comment."VERSION: ".\EnvManager::get('APP_VERSION').PHP_EOL;
        $headers .= $empty;

        $headers .= $comment."List of tables for backup: ".$tables->implode(', ').PHP_EOL;
        $headers .= PHP_EOL;

        $contents = $this->exportDatabaseToArrayFile($tables);

        return "<?php {$headers} return {$contents};";
    }

    protected function exportDatabaseToArrayFile(Collection $tables): string
    {
        $contents = [];

        foreach ($tables as $table) {
            $offset = 0;
            $limit = 1000;

            $contents[$table] = [
                'columns' => Schema::getColumnListing($table),
                'values' => [],

            ];

            do {
                $results = \DB::table($table)
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

                $countResults = $results->count();

                foreach ($results as $row) {
                    $contents[$table]['values'][] = array_values((array) $row);
                }

                unset($results);

                $offset += $limit;
            } while ($countResults === $limit);
        }

        return var_export($contents, true);
    }

    protected function backupDirectories(Zipper $ziparchive, array $options = [])
    {
        foreach ($options as $option) {
            foreach ($this->directories($option) as $action => $directories) {
                foreach ($directories as $directory) {
                    switch ($action) {
                        case 'include':
                            $ziparchive->addDirectory($this->basePath($directory), $directory);
                            break;

                        case 'exclude':
                            $ziparchive->deleteDirectory($directory, true);
                            break;

                        case 'create':
                            $ziparchive->addEmptyDirectory($directory);
                            break;

                        default:
                            // code...
                            break;
                    }
                }
            }
        }
    }

    protected function backupFiles(Zipper $ziparchive, array $files = [])
    {
        foreach ($files as $file) {
            $ziparchive->addFile($this->basePath($file), $file);
        }
    }

    /**
     * Сгенерировать новое имя файла для резервной копии.
     * @return string
     */
    public function generateBackupFilename(): string
    {
        return date('Y_m_d_His')
            .'_backup_'
            .Str::slug(config('app.name'))
            .'.'
            .Archivist::FILE_EXTENSION_BACKUP;
    }
}
