<div class="row js-pubpart-row">
    <div class="col-sm-2">
        @include('widgets.form.formitem._NUMBER', [
            'name'  => $varname.'[sequence_number]',
            'value' => $count
        ])
    </div>

    <div class="col-sm-10 js-non-periodic {{ $is_periodic ? 'hidden' : '' }}">
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

    <div class="col-sm-4 js-periodic {{ $is_periodic ? '' : 'hidden' }}">
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
</div>