@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('content')
        <h2>{{ trans('navigation.gramsets') }}</h2>
        
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/dict/gramset/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_select', 
                ['name' => 'pos_id', 
                 'values' =>$pos_values,
                 'value' =>$pos_id,
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        @if ($gramsets && $gramsets->count())
        <br>
        <table class="table">
        <thead>
            <tr><th>No</th>
                <th>{{ trans('dict.case') }}</th>
                <th>{{ trans('dict.person') }}</th>
                <th>{{ trans('dict.number') }}</th>
                <th>{{ trans('dict.tense') }}</th>
                <th>{{ trans('dict.mood') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gramsets as $key=>$gramset)
            <tr>
                <td>{{$key+1}}</td>
                <td>
                    <!-- Case (Nominative, ...) -->
                    @if($gramset->gram_id_case)
                        {{$gramset->gramCase->name}}
                        @if($gramset->gramCase->name_short)
                            ({{$gramset->gramCase->name_short}})
                        @endif
                    @endif
                </td>
                <td>
                    <!-- Person (1st, ...) -->
                    @if($gramset->gram_id_person)
                        {{$gramset->gramPerson->name}}
                        @if($gramset->gramPerson->name_short)
                            ({{$gramset->gramPerson->name_short}})
                        @endif
                    @endif
                </td>
                <td>
                    <!-- Number (singular, plural) -->
                    @if($gramset->gram_id_number)
                        {{$gramset->gramNumber->name}}
                        @if($gramset->gramNumber->name_short)
                            ({{$gramset->gramNumber->name_short}})
                        @endif
                    @endif
                </td>
                <td>
                    <!-- Tense (Past, Present, ...) -->
                    @if($gramset->gram_id_tense)
                        {{$gramset->gramTense->name}}
                        @if($gramset->gramTense->name_short)
                            ({{$gramset->gramTense->name_short}})
                        @endif
                    @endif
                </td>
                <td>
                    <!-- Mood (indicative, ...) -->
                    @if($gramset->gram_id_mood)
                        {{$gramset->gramMood->name}}
                        @if($gramset->gramMood->name_short)
                            ({{$gramset->gramMood->name_short}})
                        @endif
                    @endif  
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
@stop


