<p class="help-block">
    {{ trans('collocation_candidates.total', [
        'num' => $candidate_page['total'],
    ]) }}

    @if ($candidate_page['total'])
        {{ trans('collocation_candidates.page_of', [
            'page' => $candidate_page['page'],
            'pages' => $candidate_page['last_page'],
        ]) }}
    @endif
</p>

@if ($candidate_page['last_page'] > 1)
    <nav class="candidate-pagination"
         aria-label="{{ trans('collocation_candidates.pages') }}">
        {!! $candidate_page['paginator']->render() !!}
    </nav>
@endif