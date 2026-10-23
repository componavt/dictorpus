@if ($history->key === 'collocation_deleted')
    <?php
    $snapshot = json_decode($history->old_value, true);

    $category_name = null;

    if (is_array($snapshot)) {
        $category_name = $snapshot['concept_category_name_ru'] ?? null;

        if (app()->getLocale() === 'en') {
            $category_name = !empty($snapshot['concept_category_name_en'])
                ? $snapshot['concept_category_name_en']
                : $category_name;
        }
    }
    ?>

    @if (is_array($snapshot))
        {{ trans('history.collocation_deleted_for_meaning', [
            'id' => $snapshot['id'] ?? '?',
            'num' => $snapshot['meaning_n'] ?? '?',
        ]) }}:
        <br>

        {{ $snapshot['lemma'] ?? '?' }}
        +
        <span>{{ $snapshot['collocate_lemma'] ?? '?' }}<sup>{{ $snapshot['collocate_meaning_n'] ?? '?' }}</sup></span>

        ‘{{ $snapshot['display_gloss'] ?? '' }}’

        <br>

        {{ trans('history.collocation_category') }}:
        {{ $category_name ?: trans('history.collocation_without_category') }}

        @if (!empty($snapshot['concept_category_id']))
            ({{ $snapshot['concept_category_id'] }})
        @endif
    @else
        {{ trans('history.collocation_snapshot_unreadable') }}
    @endif
@else
    <?php
    $field_keys = [
        'meaning_id' => 'history.collocation_source_meaning',
        'collocate_meaning_id' => 'history.collocation_partner_meaning',
        'concept_category_id' => 'history.collocation_category',
        'display_gloss' => 'history.collocation_display_gloss',
    ];

    $field_name = isset($field_keys[$history->key])
        ? trans($field_keys[$history->key])
        : $history->key;

    $old_value = $history->old_value;
    $new_value = $history->new_value;

    $meaning_n = $history->collocation_meaning_n;

    $event_key = $history->key === 'created_at'
        ? 'history.collocation_created'
        : 'history.collocation_changed';

    if ($meaning_n !== null) {
        $event_key .= '_for_meaning';
    }
    ?>

    {{ trans($event_key, [
        'id' => $history->revisionable_id,
        'num' => $meaning_n,
    ]) }}

    @if ($history->key !== 'created_at')
        <br>

        {{ $field_name }}:

        <span class="old-value">
            {{ $old_value === null || $old_value === ''
                ? trans('history.collocation_not_specified')
                : $old_value }}
        </span>

        →

        <span class="new-value">
            {{ $new_value === null || $new_value === ''
                ? trans('history.collocation_not_specified')
                : $new_value }}
        </span>
    @endif
@endif