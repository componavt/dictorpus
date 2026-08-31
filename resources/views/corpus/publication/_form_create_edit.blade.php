@php
    $is_periodic = (int) old(
        'is_periodic',
        $publication->is_periodic ?? 0
    ) === 1;

    $with_photo = !empty($with_photo);
    $publication_data_col = $with_photo
        ? 'col-sm-5'
        : 'col-sm-9';
@endphp

@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<input type="hidden" id="publication_field" value="">
<div class="row">
    <div class='col-sm-3'>
        @include('widgets.form.formitem._radio', [
            'name' => 'is_periodic',
            'values' => trans('messages.bin_answers'),
            'checked' => old('is_periodic', $publication->is_periodic ?? 0), 
            'title'=>trans('corpus.is_periodic')])

        @include('widgets.form.formitem._select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lang_id']])
                 
        <div class="js-non-periodic {{ $is_periodic ? 'hidden' : '' }}">
        @include('widgets.form.formitem._text', [
            'name'  => 'year',
            'title' => trans('messages.year'),
            'value' => old('year', $publication->year ?? '')
        ])
        </div>
    </div>
    <div class='{{ $publication_data_col }}'>
        @include('widgets.form.formitem._text', [
            'name' => 'authors', 
            'title'=>trans('corpus.authors')])

        @include('widgets.form.formitem._text', [
            'name' => 'title', 
            'title'=>trans('corpus.title')])

        @include('widgets.form.formitem._text', [
            'name' => 'addition_info', 
            'title'=>trans('corpus.addition_info')])
    </div>
@if ($with_photo)
    <div class="col-sm-4">
        <div class="form-group">
            <label for="photo">Фотография</label>
            <div style="margin-bottom: 10px;">
            @include('corpus.publication.photo')
            </div>
            <input id="photo" name="photo" type="file" accept="image/*" class="form-control">
        </div>
    </div>
@endif
</div>
<h2>
    <span class="js-non-periodic {{ $is_periodic ? 'hidden' : '' }}">
        {{ trans('corpus.sections') }}
    </span>

    <span class="js-periodic {{ $is_periodic ? '' : 'hidden' }}">
        {{ trans('corpus.issues') }}
    </span>

    <i id="add-publication-pubpart"
       class="call-add fa fa-plus fa-lg"
       title="{{ trans('messages.create_new_f') }}">
    </i>
</h2>
<div class="row">
    <div class="col-sm-1"
         style="font-weight:bold; text-align:center; padding-bottom:10px">
        №
    </div>

    <div class="col-sm-10 js-non-periodic {{ $is_periodic ? 'hidden' : '' }}"
         style="font-weight:bold; text-align:center; padding-bottom:10px">
        {{ mb_strtolower(trans('corpus.title')) }}
    </div>

    <div class="col-sm-2 js-periodic {{ $is_periodic ? '' : 'hidden' }}"
         style="font-weight:bold; text-align:center; padding-bottom:10px">
        {{ trans('corpus.number') }}
    </div>

    <div class="col-sm-4 js-periodic {{ $is_periodic ? '' : 'hidden' }}"
         style="font-weight:bold; text-align:center; padding-bottom:10px">
        {{ trans('messages.year') }}
    </div>

    <div class="col-sm-4 js-periodic {{ $is_periodic ? '' : 'hidden' }}"
         style="font-weight:bold; text-align:center; padding-bottom:10px">
        {{ trans('corpus.issue_date') }}
    </div>

    <div class="col-sm-1"
         style="padding-bottom:10px">
    </div>
</div>
@php
    $lastSequence = !empty($publication) && $publication->pubparts->count()
        ? $publication->pubparts->max('sequence_number') : 0;

    $count = $lastSequence + 1;
@endphp

<div id="publication-pubparts" data-next-index="{{ $count }}">

@if (!empty($publication))
    @foreach ($publication->pubparts as $pubpart)
        @include('corpus.pubpart._form_create_edit', [
            'count' => $pubpart->sequence_number,
            'pubpart' => $pubpart,
            'varname' => 'pubparts['.$pubpart->id.']',
            'is_periodic' => $is_periodic
        ])
    @endforeach
@endif

    @include('corpus.pubpart._form_create_edit', [
        'count' => $count,
        'pubpart' => null,
        'varname' => 'new_pubparts['.$count.']',
        'is_periodic' => $is_periodic
    ])
</div>

<script type="text/template" id="publication-pubpart-template">
    @include('corpus.pubpart._form_create_edit', [
        'count' => '__INDEX__',
        'pubpart' => null,
        'varname' => 'new_pubparts[__INDEX__]',
        'is_periodic' => $is_periodic
    ])
</script>