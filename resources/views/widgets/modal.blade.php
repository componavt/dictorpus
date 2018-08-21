<div class="modal fade in" id="{{ $name }}" tabindex="-1" role="dialog" aria-hidden="false" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body">
                @include($modal_view) 
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="{{ $submit_id }}">{{ $submit_title }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('messages.close')}}</button>
            </div>
        </div>
    </div>
</div>