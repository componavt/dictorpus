        <div class="fragment-view">
            <b>{{ trans('corpus.fragment') }}</b><br>
              {!! $text_xml !!}
              <i class="fa fa-pencil-alt fa-lg fragment-edit" title="{{trans('messages.edit')}}"
                 onClick="editFragment({{$id}})"></i>
        </div>
