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

    public function pubparts()
    {
        return $this->belongsToMany(
            Pubpart::class,
            'pubpart_source',
            'source_id',
            'pubpart_id'
        )->withPivot('pages');
    }

    public function bookToString()
    {
        if ($this->publication_id) {
            return $this->publicationToString();
        }
        $book = [];

        if ($this->author) {
            $book[] = $this->author;
        }
        if ($this->title) {
            $book[] = $this->title;
        }
        if ($this->year) {
            //            $book[] = '('.$this->year.')';
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
     * if Source doesn't exist, 
     *      creates new and returns id of Source
     * elseif Source is updated (data of Source is modified)
     *      if other texts with this Source exist, 
     *          creates new and returns id of Source
     *      else 
     *          updates Source if it exists 
     * 
     * @param INT $source_id or NULL
     * @param ARRAY $data_to_fill
     * @return INT or NULL
     */
    public static function fillByData($source_id, $request_data)
    {
        $source_fields = ['publication_id', 'title', 'author', 'year', 'ieeh_archive_number1', 'ieeh_archive_number2', 'pages', 'comment'];
        foreach ($source_fields as $column) {
            $data_to_fill[$column] = ($request_data['source_' . $column]) ? $request_data['source_' . $column] : NULL;
        }
        if (!$source_id) {
            $source = Source::firstOrCreate($data_to_fill);
            $source_id = $source->id;
        } else {
            $source = Source::find($source_id);
            $source_is_updated = false;
            foreach ($data_to_fill as $column => $data_value) {
                if ($data_value != $source->$column) {
                    $source_is_updated = true;
                }
            }
            if ($source_is_updated) {
                if ($source->texts && $source->texts()->count() > 1) { // other texts with this Source exist
                    $source_new = Source::firstOrCreate($data_to_fill);
                    $source_id = $source_new->id;
                } else {
                    $source->fill($data_to_fill);
                    $source->save();
                }
            }
        }
        return $source_id;
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

    public function storePubparts(array $data)
    {
        $pubpartRows = isset($data['pubparts'])
            ? $data['pubparts']
            : [];

        $pivotData = [];

        foreach ($pubpartRows as $row) {
            $pubpartId = isset($row['pubpart_id'])
                ? (int) $row['pubpart_id']
                : 0;

            if (!$pubpartId) {
                continue;
            }

            $pages = isset($row['pages'])
                ? trim($row['pages'])
                : null;

            $pivotData[$pubpartId] = [
                'pages' => $pages ?: null,
            ];
        }

        return $this->pubparts()->sync($pivotData);
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

        return $result
            ? $result . '. ' . $part
            : $part;
    }
}
