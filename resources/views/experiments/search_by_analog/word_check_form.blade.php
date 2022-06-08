        {!! Form::open(['url' => '/experiments/search_by_analog/check_word', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        <INPUT type="hidden" name="property" value="{{$property}}">
        <INPUT type="hidden" name="search_lang" value="{{$search_lang}}">
        <table>
            <tr><td>
        @include('widgets.form.formitem._text', 
                ['name' => 'word', 
                'attributes'=>['placeholder' => 'word', 'size'=>10]])
            </td>
            <td>
        @include('widgets.form.formitem._submit', ['title' => $submit_value])
            </td></tr>
        </table>
        {!! Form::close() !!}
