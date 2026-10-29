<tr>
    <td title="{{ trans('collocation_candidates.meaning_hint') }}">
        <a href="{{ $item['meaning_url'] }}">{{ $item['meaning_n'] }}.</a>
        {{ $item['gloss'] }}
    </td>

    @foreach (['sentences', 'checked', 'unchecked', 'texts'] as $column)
        <?php
        $field = [
            'sentences' => 'sentences',
            'checked' => 'checked_sentences',
            'unchecked' => 'unchecked_sentences',
            'texts' => 'texts',
        ][$column];
        ?>
        <td class="candidate-count @if ($column === 'unchecked') candidate-unchecked @endif"
            title="{{ trans('collocation_candidates.'.$column.'_hint') }}">
            {{ $item[$field] }}
        </td>
    @endforeach

    <td title="{{ trans('collocation_candidates.recorded_hint') }}">
        @include('dict.meaning.candidates._recorded')
    </td>

    <td class="candidate-actions" title="{{ trans('collocation_candidates.actions_hint') }}">
        <a href="{{ $item['examples_url'] }}">{{ trans('collocations.matrix_examples') }}</a>

        @if (User::checkAccess('dict.edit'))
            @if ($item['recorded'])
                @include('dict.meaning.candidates._delete')
            @else
                <a href="{{ $item['editor_url'] }}">{{ trans('collocations.add') }}</a>
            @endif
        @endif
    </td>
</tr>