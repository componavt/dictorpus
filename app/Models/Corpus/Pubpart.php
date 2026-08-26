<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

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
