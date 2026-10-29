<div class="candidate-table-scroll" tabindex="0" role="region"
     aria-label="{{ trans('collocation_candidates.title') }}">
    <table class="table table-bordered candidate-table">
        <thead>
            <tr>
                @foreach (['meaning', 'sentences', 'checked', 'unchecked', 'texts', 'recorded', 'actions'] as $column)
                    <th scope="col" title="{{ trans('collocation_candidates.'.$column.'_hint') }}">
                        {{ trans('collocation_candidates.'.$column) }}
                    </th>
                @endforeach
            </tr>
        </thead>

        @foreach ($groups as $group)
            <tbody>
                <tr class="candidate-lemma">
                    <th colspan="7" scope="rowgroup">
                        <a href="{{ $group['url'] }}">{{ $group['lemma'] }}</a>
                        <span class="text-muted">{{ $group['pos'] }}</span>
                    </th>
                </tr>

                @foreach ($group['items'] as $item)
                    @include('dict.meaning.candidates._row')
                @endforeach
            </tbody>
        @endforeach
    </table>
</div>