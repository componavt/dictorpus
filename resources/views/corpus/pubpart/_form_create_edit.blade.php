<div
    class="row js-pubpart-row"
    data-pubpart-id="{{ !empty($pubpart) ? $pubpart->id : '' }}"
>
    <div class="col-sm-2">
        @include('widgets.form.formitem._NUMBER', [
            'name'  => $varname.'[sequence_number]',
            'value' => $count
        ])
    </div>

    <div class="col-sm-9 js-non-periodic {{ $is_periodic ? 'hidden' : '' }}">
        @include('widgets.form.formitem._text', [
            'name'  => $varname.'[title]',
            'value' => $pubpart->title ?? ''
        ])
    </div>

    <div class="col-sm-2 js-periodic {{ $is_periodic ? '' : 'hidden' }}">
        @include('widgets.form.formitem._text', [
            'name'  => $varname.'[number]',
            'value' => $pubpart->number ?? ''
        ])
    </div>

    <div class="col-sm-3 js-periodic {{ $is_periodic ? '' : 'hidden' }}">
        @include('widgets.form.formitem._NUMBER', [
            'name'  => $varname.'[year]',
            'value' => $pubpart->year ?? '',
            'class' => 'pubpart_year'
        ])
    </div>

    <div class="col-sm-4 js-periodic {{ $is_periodic ? '' : 'hidden' }}">
        @include('widgets.form.formitem._DATE', [
            'name'  => $varname.'[issue_date]',
            'value' => $pubpart->issue_date ?? ''
        ])
    </div>

    <div class="col-sm-1 text-center">
        <button
            type="button"
            class="remove-publication-pubpart btn btn-link"
            title="{{ trans('messages.delete') }}"
            aria-label="{{ trans('messages.delete') }}"
        >
            <i class="fa fa-times"></i>
        </button>
    </div>
</div>