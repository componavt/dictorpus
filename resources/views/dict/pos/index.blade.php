@extends('layouts.page')

@section('page_title')
{{ trans('navigation.parts_of_speech') }}
@stop

@section('body')
        <div class="row">
        @foreach($pos_by_categories as $category => $parts_of_speech)
            <div class="col-sm-4">
                <h3>{{ trans('dict.pos_category_'.$category) }}</h3>
                @foreach($parts_of_speech as $pos)
                <p>
                    @if (User::checkAccess('admin'))
                        <a href="{{ LaravelLocalization::localizeURL('/dict/pos/'.$pos->id) }}">{{ $pos->name }}</a> 
                    @else
                    {{ $pos->name }} 
                    @endif
                    ({{ $pos->code }})
                    @if (User::checkAccess('admin'))
                        @include('widgets.form.button._edit', ['route' => '/dict/pos/'.$pos->id.'/edit', 'without_text' => 1])                        
                    @endif
                </p>
                @endforeach
            </div>
        @endforeach
        </div>
@stop


