@php
    $sourcePubparts = $source_pubparts ?? collect();
@endphp

<div id="source-pubparts-group"
     class="hidden"
     data-sections="{{ trans('corpus.sections') }}"
     data-issues="{{ trans('corpus.issues') }}">

    <div id="source-pubparts-loader"
         class="text-center hidden"
         style="padding: 10px;">
        <i class="fa fa-spinner fa-spin fa-lg"></i>
        Загрузка частей публикации…
    </div>

    <div id="source-pubparts-content">

        <div class="form-group">
            <label>
                {{ trans('corpus.pubparts') }}

                <i id="add-source-pubpart"
                class="call-add fa fa-plus fa-lg"
                title="{{ trans('messages.create_new_f') }}">
                </i>
            </label>

            <div class="row source-pubparts-header">
                <div class="col-sm-8">
                    <b class="source-pubparts-column-title"></b>
                </div>

                <div class="col-sm-3">
                    <b>{{ trans('corpus.source_pages') }}</b>
                </div>

                <div class="col-sm-1"></div>
            </div>

            <div id="source-pubparts"
                data-next-index="{{ $sourcePubparts->count() }}">

                @foreach ($sourcePubparts as $index => $pubpart)
                    <div class="row source-pubpart-row">
                        <div class="col-sm-7">
                            <select class="form-control select-pubpart"
                                    name="source[pubparts][{{ $index }}][pubpart_id]">
                                <option value="{{ $pubpart->id }}" selected>
                                    {{ $pubpart->full_name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-1">
                            <i class="create-source-pubpart call-add fa fa-plus fa-lg"
                            title="Создать новую часть публикации">
                            </i>
                        </div>
                        <div class="col-sm-3">
                            <input class="form-control"
                                type="text"
                                name="source[pubparts][{{ $index }}][pages]"
                                value="{{ $pubpart->pivot->pages }}">
                        </div>

                        <div class="col-sm-1">
                            <i class="remove-source-pubpart fa fa-times fa-lg"
                            title="{{ trans('messages.delete') }}">
                            </i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>{{-- #source-pubparts-content --}}
</div>{{-- #source-pubparts-group --}}


<script type="text/template" id="source-pubpart-template">
    <div class="row source-pubpart-row">
        <div class="col-sm-7">
            <select class="form-control select-pubpart"
                    name="source[pubparts][__INDEX__][pubpart_id]">
                <option value=""></option>
            </select>
        </div>

        <div class="col-sm-1">
            <i class="create-source-pubpart call-add fa fa-plus fa-lg"
               title="Создать новую часть публикации">
            </i>
        </div>

        <div class="col-sm-3">
            <input class="form-control"
                   type="text"
                   name="source[pubparts][__INDEX__][pages]"
                   value="">
        </div>

        <div class="col-sm-1">
            <i class="remove-source-pubpart fa fa-times fa-lg"
               title="{{ trans('messages.delete') }}">
            </i>
        </div>
    </div>
</script>