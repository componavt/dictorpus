<div id="example-collocation-modal"
     class="modal fade"
     tabindex="-1"
     role="dialog"
     aria-labelledby="example-collocation-modal-title"
     data-loading="{{ trans('example_collocations.loading') }}"
     data-load-error="{{ trans('example_collocations.load_error') }}"
     data-save-error="{{ trans('example_collocations.save_error') }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ trans('collocations.cancel') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="example-collocation-modal-title">
                    {{ trans('collocations.add') }}
                </h4>
            </div>
            <div class="modal-body example-collocation-modal-body"></div>
        </div>
    </div>
</div>
