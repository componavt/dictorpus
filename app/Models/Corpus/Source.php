<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Text;

class Source extends Model
{
    protected $fillable = ['publication_id', 'title', 'author', 'year', 'ieeh_archive_number1', 'ieeh_archive_number2', 'pages', 'comment'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Publication;

    // Has Many Relations
    use \App\Traits\Relations\HasMany\Texts;

    // Methods
    use \App\Traits\Methods\source\PublicationToString;

    public function bookToString($text = null)
    {
        if ($this->publication_id) {
            return $this->publicationToString($text);
        }
        $book = [];

        if ($this->author) {
            $book[] = $this->author;
        }
        if ($this->title) {
            $book[] = $this->title;
        }
        if ($this->year) {
            $book[] = $this->year;
        }
        if ($this->pages && (int)$this->pages > 0) {
            $book[] = trans('corpus.p') . ' ' . $this->pages;
        }

        $book = join(', ', $book);

        if ($this->pages && !(int)$this->pages) {
            $book .= '. ' . $this->pages;
        }

        return $book;
    }

    /**
     * Находит либо создаёт Source по библиографическим данным
     * и возвращает его ID.
     *
     * Source теперь может быть общим для нескольких текстов:
     * части публикации и страницы относятся к Text через
     * таблицу pubpart_text, а не к Source.
     *
     * @param int|null $source_id
     * @param array $request_data
     * @return int
     */
    public static function fillByData($request_data)
    {
        $source_fields = [
            'publication_id',
            'title',
            'author',
            'year',
            'ieeh_archive_number1',
            'ieeh_archive_number2',
            'pages',
            'comment',
        ];

        $data_to_fill = [];

        foreach ($source_fields as $column) {
            $request_key = 'source_' . $column;

            $value = isset($request_data[$request_key]) ? $request_data[$request_key] : null;

            // Пустая строка и отсутствующее поле должны совпадать с NULL в существующих source.
            if (is_string($value)) {
                $value = trim($value);
            }

            $data_to_fill[$column] = ($value === '') ? null : $value;
        }

        /* Каждый раз ищем source по полному набору данных.
        * $source_id намеренно больше не определяет, какой Source
        * возвращать: старый source может быть одним из сотен
        * одинаковых дубликатов. */
        $source = self::firstOrCreate($data_to_fill);

        return $source->id;
    }

    public static function removeByID($id)
    {
        $obj = self::find($id);
        if (!$obj || $obj->texts) {
            return;
        }
        $obj->delete();
    }

    /**
     * remove source if exists and don't link with other texts
     * 
     * @param INT $source_id
     * @param INT $text_id
     */
    public static function removeUnused($source_id, $text_id)
    {
        if ($source_id && !Text::where('id', '<>', $text_id)
            ->where('source_id', $source_id)
            ->count()) {
            Source::find($source_id)->delete();
        }
    }

    protected function oldPagesToString()
    {
        $pages = trim($this->pages ?: '');

        if (!$pages) {
            return null;
        }

        return 'С. ' . $pages;
    }

    protected function appendSourceInfo($result, $part)
    {
        if (!$part) {
            return $result;
        }

        if (!$result) {
            return $part;
        }

        $result = rtrim($result);

        if (substr($result, -1) !== '.') {
            $result .= '.';
        }

        return $result . ' ' . $part;
    }
}
