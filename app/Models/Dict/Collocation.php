<?php

namespace App\Models\Dict;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Collocation extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    use \App\Traits\Relations\BelongsTo\Meaning;

    protected $table = 'collocations';

    protected $fillable = [
        'meaning_id',
        'collocate_meaning_id',
        'concept_category_id',
        'display_gloss',
    ];

    protected $casts = [
        'meaning_id' => 'integer',
        'collocate_meaning_id' => 'integer',
    ];

    protected $revisionEnabled = true;
    protected $revisionCleanup = true;
    protected $historyLimit = 500;
    protected $revisionCreationsEnabled = true;

    /**
     * Значение слова-партнёра в сочетании.
     */
    public function collocateMeaning()
    {
        return $this->belongsTo(
            Meaning::class,
            'collocate_meaning_id'
        );
    }

    /**
     * Категория сочетаемости выбранной пары значений.
     */
    public function conceptCategory()
    {
        return $this->belongsTo(
            ConceptCategory::class,
            'concept_category_id'
        );
    }

    /**
     * Формирует снимок сочетаемости с подписями для истории удаления.
     *
     * @return array
     */
    public function historySnapshot()
    {
        $meaning = $this->meaning()->with('lemma')->firstOrFail();

        $collocate_meaning = $this->collocateMeaning()
            ->with('lemma')
            ->firstOrFail();

        $category = $this->conceptCategory()->first();

        return [
            'id' => $this->id,

            'meaning_id' => $meaning->id,
            'meaning_n' => $meaning->meaning_n,
            'lemma_id' => $meaning->lemma_id,
            'lemma' => $meaning->lemma->lemma,

            'collocate_meaning_id' => $collocate_meaning->id,
            'collocate_meaning_n' => $collocate_meaning->meaning_n,
            'collocate_lemma_id' => $collocate_meaning->lemma_id,
            'collocate_lemma' => $collocate_meaning->lemma->lemma,

            'concept_category_id' => $this->concept_category_id,
            'concept_category_name_ru' => $category ? $category->name_ru : null,
            'concept_category_name_en' => $category ? $category->name_en : null,

            'display_gloss' => $this->display_gloss,
        ];
    }

    /**
     * Удаляет сочетаемость и сохраняет снимок в истории исходной леммы.
     * Запись события и удаление выполняются в одной транзакции.
     *
     * @return bool
     */
    public function removeWithHistory()
    {
        $collocation_id = $this->getKey();

        $result = DB::transaction(function () use ($collocation_id) {
            $collocation = static::where('id', $collocation_id)
                ->lockForUpdate()->firstOrFail();

            $snapshot = $collocation->historySnapshot();

            $snapshot_json = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($snapshot_json === false) {
                throw new \RuntimeException('Не удалось сформировать снимок сочетаемости.');
            }

            $lemma = $collocation->meaning->lemma;

            if (!createRevisionRecord($lemma, 'collocation_deleted', $snapshot_json, null)) {
                throw new \RuntimeException('Не удалось записать историю удаления сочетаемости.');
            }

            if (!$collocation->delete()) {
                throw new \RuntimeException('Не удалось удалить сочетаемость.');
            }

            return true;
        });

        $this->exists = false;

        return $result;
    }
}
