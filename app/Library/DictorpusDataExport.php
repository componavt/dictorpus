<?php

namespace App\Library;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

use App\Library\SynonymExport;
use App\Models\Dict\Lang;

class DictorpusDataExport
{
    const LANGUAGES = ['krl', 'lud', 'olo', 'vep'];

    public static function filenames()
    {
        $files = [];

        foreach (self::LANGUAGES as $code) {
            $files[] = 'dict/meanings_' . $code . '.csv.zst';
            $files[] = 'dict/examples_' . $code . '.csv.zst';
            $files[] = 'dict/morphology/lemmas_' . $code . '.csv.zst';
            $files[] = 'dict/morphology/lemma_wordforms_' . $code . '.csv.zst';

            foreach (['texts', 'sentences', 'words', 'candidate_analyses'] as $name) {
                $files[] = 'corpus/' . $name . '_' . $code . '.csv.zst';
            }
        }

        foreach (SynonymExport::filenames() as $filename) {
            if ($filename !== 'meanings_olo.csv') {
                $files[] = 'dict/synonyms/' . $filename . '.zst';
            }
        }

        foreach (['grams', 'gramsets', 'gramset_pos', 'parts_of_speech'] as $name) {
            $files[] = 'dict/grammar/' . $name . '.csv';
        }

        foreach (['corpora', 'genres', 'dialects'] as $name) {
            $files[] = 'corpus/reference/' . $name . '.csv';
        }

        sort($files);

        return $files;
    }

    public function run()
    {
        $lock = fopen(storage_path('app/dictorpus-data-export.lock'), 'c');

        if ($lock === false) {
            throw new \RuntimeException('Не удалось открыть блокировку экспорта.');
        }

        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);

            throw new \RuntimeException('Экспорт dictorpus-data уже выполняется.');
        }

        $relative = 'export/.dictorpus-data-' . uniqid('', true);
        $temporary = storage_path('app/public/' . $relative);
        $target = storage_path('app/public/export/dictorpus-data');
        $backup = $target . '-backup-' . uniqid('', true);

        try {
            $this->process('zstd --version', 30);

            foreach (['dict', 'corpus', 'dict/morphology', 'dict/grammar', 'corpus/reference'] as $directory) {
                $path = $temporary . '/' . $directory;

                if (!File::makeDirectory($path, 0755, true) && !is_dir($path)) {
                    throw new \RuntimeException('Не удалось создать каталог ' . $path);
                }
            }

            $synonymResult = (new SynonymExport())->run(
                $relative . '/dict/synonyms'
            );

            if (!rename(
                $temporary . '/dict/synonyms/meanings_olo.csv',
                $temporary . '/dict/meanings_olo.csv'
            )) {
                throw new \RuntimeException(
                    'Не удалось переместить meanings_olo.csv в dict/.'
                );
            }

            $methods = [
                'texts' => 'textsforMorphDisambig',
                'sentences' => 'sentencesforMorphDisambig',
                'words' => 'wordsforMorphDisambig',
                'candidate_analyses' => 'candidateAnalysesforMorphDisambig',
            ];

            foreach (self::LANGUAGES as $code) {
                $lang = Lang::where('code', $code)->first();

                if (!$lang) {
                    throw new \RuntimeException('Не найден язык ' . $code);
                }

                if ($code !== 'olo') {
                    if (!Export::russianMeanings(
                        $relative . '/dict/meanings_' . $code . '.csv',
                        $lang
                    )) {
                        throw new \RuntimeException(
                            'Ошибка экспорта meanings_' . $code
                        );
                    }
                }

                Export::russianTranslations($relative . '/dict/examples_' . $code . '.csv', $lang->id);

                foreach ($methods as $name => $method) {
                    Export::$method($lang->id, $relative . '/corpus/' . $name . '_' . $code . '.csv');
                }

                MorphologyExport::lemmas($lang->id, $relative . '/dict/morphology/lemmas_' . $code . '.csv');
                MorphologyExport::lemmaWordforms($lang->id, $relative . '/dict/morphology/lemma_wordforms_' . $code . '.csv');
            }

            MorphologyExport::grams($relative . '/dict/grammar/grams.csv');
            MorphologyExport::gramsets($relative . '/dict/grammar/gramsets.csv');
            MorphologyExport::gramsetPos($relative . '/dict/grammar/gramset_pos.csv');
            MorphologyExport::partsOfSpeech($relative . '/dict/grammar/parts_of_speech.csv');

            CorpusReferenceExport::corpora($relative . '/corpus/reference/corpora.csv');
            CorpusReferenceExport::genres($relative . '/corpus/reference/genres.csv');
            CorpusReferenceExport::dialects($relative . '/corpus/reference/dialects.csv');

            foreach (self::filenames() as $filename) {
                if (substr($filename, -4) === '.zst') {
                    $this->compress($temporary . '/' . substr($filename, 0, -4));
                    continue;
                }

                $path = $temporary . '/' . $filename;

                if (!is_file($path) || filesize($path) === 0) {
                    throw new \RuntimeException('CSV отсутствует или пуст: ' . $filename);
                }
            }

            if (file_exists($target)) {
                if (!is_dir($target) || !rename($target, $backup)) {
                    throw new \RuntimeException('Не удалось сохранить предыдущую выгрузку.');
                }
            }

            if (!rename($temporary, $target)) {
                if (is_dir($backup) && !rename($backup, $target)) {
                    throw new \RuntimeException('Ошибка публикации. Предыдущая выгрузка сохранена: ' . $backup);
                }

                throw new \RuntimeException('Не удалось опубликовать экспорт dictorpus-data.');
            }

            if (is_dir($backup)) {
                File::deleteDirectory($backup);
            }

            return [
                'file_count' => count(self::filenames()),
                'synonyms' => $synonymResult,
            ];
        } finally {
            if (is_dir($temporary)) {
                File::deleteDirectory($temporary);
            }

            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function compress($filename)
    {
        if (!is_file($filename) || filesize($filename) === 0) {
            throw new \RuntimeException(
                'CSV отсутствует или пуст: ' . $filename
            );
        }

        $compressed = $filename . '.zst';

        $this->process(
            'zstd -q -19 -f -o '
                . escapeshellarg($compressed)
                . ' -- '
                . escapeshellarg($filename)
        );

        if (!is_file($compressed) || filesize($compressed) === 0) {
            throw new \RuntimeException(
                'Не создан сжатый файл: ' . $compressed
            );
        }

        $this->process(
            'zstd -q -t -- ' . escapeshellarg($compressed)
        );

        if (!unlink($filename)) {
            throw new \RuntimeException(
                'Не удалось удалить рабочий CSV: ' . $filename
            );
        }
    }

    protected function process($command, $timeout = null)
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                'Ошибка внешней команды: '
                    . $command . "\n"
                    . trim(
                        $process->getErrorOutput()
                            . "\n"
                            . $process->getOutput()
                    )
            );
        }
    }
}
