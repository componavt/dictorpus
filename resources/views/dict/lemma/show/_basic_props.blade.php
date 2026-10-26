@if (empty($lemma->is_norm))
    <p>
        <b>{{ trans('dict.is_norm_values')[0] }}</b>
    </p>
@endif

<p>
    <b>{{ trans('dict.lang') }}:</b>
    {{ $lemma->lang->name }}
</p>

@if ($lemma->pos)
    <p>
        <b>{{ trans('dict.pos') }}:</b>
        {{ $lemma->pos->name }}
        {{ $lemma->featsToString() }}
    </p>
@endif