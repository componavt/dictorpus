<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pubpart extends Model
{
    public $timestamps = false;
    protected $fillable = ['publication_id', 'issue_date', 'number', 'title', 'year', 'sequence_number'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    public function identifiableName()
    {
        return $this->name;
    }

    // Belongs To Many Relations    
    use \App\Traits\Relations\BelongsToMany\Publications;

    // Methods
    use \App\Traits\Methods\search\byID;

    public function getNameAttribute(): String
    {
        return $this->number ?? $this->title;
    }

    public function getFullNameAttribute(): String
    {
        if ($this->number) {
            return ($this->year ? $this->year . ': ' : '') .
                '№ ' . $this->number .
                ($this->issue_date > '0000-00-00' ? ' (' . $this->issue_date . ')' : '');
        }
        return $this->title;
    }

    public function setIssueDateAttribute($value)
    {
        $this->attributes['issue_date'] = $this->normalizeIssueDate($value);
    }

    protected function normalizeIssueDate($value)
    {
        if (!$value || $value === '0000-00-00') {
            return null;
        }

        return $value;
    }

    public function texts()
    {
        $id = $this->id;
        return Text::whereNotNull('source_id')
            ->whereIn('source_id', function ($q) use ($id) {
                $q->select('source_id')->from('pubpart_source')
                    ->where('pubpart_id', $id);
            });
    }

    public function texts_count(): int
    {
        return (int) $this->texts()->count();
    }


    public function has_texts(): bool
    {
        return $this->texts()->exists();
    }


    public function deletion_error(): ?string
    {
        $texts_count = $this->texts_count();

        if (!$texts_count) {
            return null;
        }

        return 'Невозможно удалить часть публикации «' .
            $this->full_name .
            '»: она связана с текстами (' .
            $texts_count .
            '). Сначала удалите связи с частью в текстовых формах.';
    }


    public function delete_without_text_links(): bool
    {
        // Если хотя бы один source этой pubpart используется текстом, удаление запрещено.
        if ($this->has_texts()) {
            return false;
        }

        /*
         * Удаляем только промежуточные связи:
         * pubpart_source.pubpart_id = текущая часть.
         * Сами records в sources не удаляются.
         */
        DB::table('pubpart_source')
            ->where('pubpart_id', $this->id)
            ->delete();

        // Теперь pubpart больше не имеет связей в pubpart_source, поэтому её можно удалить.
        return (bool) $this->delete();
    }

    public function sourceLinksCount(): int
    {
        return (int) DB::table('pubpart_source')
            ->where('pubpart_id', $this->id)
            ->count();
    }


    public function hasSourceLinks(): bool
    {
        return $this->sourceLinksCount() > 0;
    }


    public function deletionError(): ?string
    {
        $source_links_count = $this->sourceLinksCount();

        if (!$source_links_count) {
            return null;
        }

        return 'Невозможно удалить часть публикации «' .
            $this->full_name .
            '»: она связана с источниками (' .
            $source_links_count .
            '). Сначала удалите связи с этой частью в текстовых формах.';
    }

    public static function getList()
    {
        $objs = self::orderBy('title')->get();

        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->full_name;
        }
        asort($list);

        return $list;
    }

    public static function nextSequenceNumber(Publication $publication)
    {
        return (int) $publication
            ->pubparts()
            ->max('sequence_number') + 1;
    }
}
