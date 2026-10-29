<nav class="candidate-pagination" aria-label="{{ trans('collocation_candidates.pages') }}">
    @if ($filters['page'] > 1)
        <a class="btn btn-default" href="{{ $base_url.'?'.http_build_query(array_merge($filters, ['page' => $filters['page'] - 1])) }}">
            {{ trans('collocation_candidates.previous') }}
        </a>
    @endif

    <span>{{ trans('collocation_candidates.page', ['num' => $filters['page']]) }}</span>

    @if ($candidate_page['has_more'])
        <a class="btn btn-default" href="{{ $base_url.'?'.http_build_query(array_merge($filters, ['page' => $filters['page'] + 1])) }}">
            {{ trans('collocation_candidates.next') }}
        </a>
    @endif
</nav>