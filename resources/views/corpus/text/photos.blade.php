@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('body')       
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a>            
        </p>
        
        <h2>
            {{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
            {{ $text->title }}
        </h2>
        @include('corpus.text.show.metadata')
        
        <h3>{{ trans('corpus.photos') }}</h3>
        {!! Form::model($text, ['method'=>'POST', 'route'=>['text.update.photos', $text->id], 'files'=>true] ) !!}
        <div class='row'>
        @foreach ($photos as $photo) 
            <div class='col-sm-4' style='text-align: center'>
                <div style="background-color: #ccc3">
                    <img src="{{ $photo->getUrl('thumb') }}">
                    @include('widgets.form.formitem._text', 
                            ['name' => 'photo['.$photo->id.'][title]', 
                             'value' => $photo->name,
                             'attributes'=>['placeholder'=>trans('corpus.title')]])
                             <input class="form-control" type="file" name="file_{{ $photo->id }}"><br>
                    @include('widgets.form.button._delete', 
                           ['route' => 'text.photos.destroy', 
                            'is_button' => 1,
                            'args'=>['id' => $text->id, 'photo_id'=>$photo->id]])
                </div>
            </div>
        @endforeach
        </div>
        
        <h3>{{ trans('corpus.new_photo') }}</h3>
        <div class='row'>
            <div class='col-sm-5'>
                <input class="form-control" type="file" name="new_file">
            </div>
            <div class='col-sm-5'>
            @include('widgets.form.formitem._text', 
                    ['name' => 'new_title', 
                     'attributes'=>['placeholder'=>trans('corpus.title')]])
            </div>
            <div class='col-sm-2'>
                @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
            </div>
        </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    highlightSentences();
    
{{-- show/hide a block with meanings and gramsets --}}
    showLemmaLinked({{$text->id}}); 
    
    addWordform('{{$text->id}}','{{$text->lang_id}}', '{{LaravelLocalization::getCurrentLocale()}}');
    posSelect(false);
    checkLemmaForm();
    toggleSpecial();  
    
    $(".sentence-edit").click(function() {
        var sid=$(this).data('sid');
        loadSentenceForm(sid);
    });    
@stop

