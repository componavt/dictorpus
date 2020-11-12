@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts')}}. {{ trans('navigation.help') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h3>Добавление населенного пункта, информанта, собирателя из формы текста</h3>
    <div class="row">
        <div class="col-sm-3">
        Рядом со списками населенных пунктов, информантов и собирателей расположены кнопочки-плюсы
        <i class="call-add fa fa-plus fa-lg" title="Создать новый"></i>.        
        По клику на “плюс” открывается дополнительное окно с формой, которую нужно заполнить и нажать на кнопку “сохранить”. 
        Окно закроется, а в списке появится новая запись.
        </div>
        <div class="col-sm-9">
            @include('widgets.youtube',
                    ['width' => '100%',
                     'height' => '270',
                     'video' => 'YH0GTF47Sxo'
                    ])            
        </div>
    </div>
    
    
@stop
