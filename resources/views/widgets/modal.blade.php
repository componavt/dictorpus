<div class="modal fade in" id="{{ $name }}" tabindex="-1" role="dialog" aria-hidden="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body">
                @if (isset($modal_view))
                    @include($modal_view) 
                @endif
            </div>
            <div class="modal-footer">
                @if (isset($submit_id))
                <button type="submit" class="btn btn-success" id="{{ $submit_id }}">{{ $submit_title }}</button>
                @elseif (isset($submit_onClick))
                <button type="submit" class="btn btn-success" onClick="{{ $submit_onClick }}">{{ $submit_title }}</button>
                @endif
                <button type="button" class="btn btn-default cancel" data-dismiss="modal">{{trans('messages.close')}}</button>
            </div>
        </div>
    </div>
</div>