@php
    /*
     * После возврата с ошибкой приоритета имеют значения old().
     * При обычном открытии edit-формы строим строки из связей text -> bibles.
     */
    $bible_rows = old('bibles');

    if ($bible_rows === null) {
        $bible_rows = [];

        if (!empty($text)) {
            foreach ($text->bibles as $bible) {
                $bible_rows[] = [
                    'bible_id' => $bible->id,
                    'reference_type' => $bible->pivot->reference_type,
                    'sequence_number' => $bible->sequence_number,
                    'chapter' => $bible->pivot->chapter,
                    'verse_from' => $bible->pivot->verse_from,
                    'verse_to' => $bible->pivot->verse_to,
                ];
            }
            $bible_rows = collect($bible_rows)
                ->sortBy(function ($row) {
                    return sprintf(
                        '%02d|%05d|%05d|%05d|%05d|%05d',
                        (int) $row['reference_type'],
                        (int) $row['sequence_number'],
                        (int) $row['bible_id'],
                        (int) $row['chapter'],
                        (int) $row['verse_from'],
                        (int) $row['verse_to']
                    );
                })
                ->values()
                ->all();            
        }
    }

    /*
     * Если ссылок пока нет, выводим одну пустую строку.
     * Она будет скрыта, пока пользователь не выберет корпус 2.
     */
    if (!count($bible_rows)) {
        $bible_rows[] = [
            'bible_id' => '',
            'reference_type' => 1,
            'chapter' => '',
            'verse_from' => '',
            'verse_to' => '',
        ];
    }
@endphp

<div
    id="bible-fields"
    class="hidden"
    data-next-index="{{ count($bible_rows) }}"
    data-bible-corpus-id="{{ \App\Models\Corpus\Text::BibleCorpus }}"
>
    <div class="row">
        <div class="col-sm-11">
            <h3>{{ trans('corpus.bible_links') }}</h3>
        </div>

        <div class="col-sm-1" style='text-align:center; padding-top: 30px'>
            <i
                id="add-bible-field"
                class="call-add fa fa-plus fa-lg"
                title="{{ trans('messages.create_new_f') }}"
            ></i>
        </div>
    </div>

    <div id="bible-fields-rows">
        @foreach ($bible_rows as $index => $bible_row)
            @include('corpus.text.form._bible_field_row', [
                'index' => $index,
                'bible_row' => $bible_row,
                'bible_values' => $bible_values
            ])
        @endforeach
    </div>
</div>

<script type="text/template" id="bible-field-template">
    @include('corpus.text.form._bible_field_row', [
        'index' => '__INDEX__',
        'bible_row' => [
            'bible_id' => '',
            'reference_type' => 1,
            'chapter' => '',
            'verse_from' => '',
            'verse_to' => '',
        ],
        'bible_values' => $bible_values
    ])
</script>