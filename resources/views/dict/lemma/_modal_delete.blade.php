<!--aside class="modal" id="delete_confirm">
    <header>
        <h2>Удаление элемента</h2>
    </header>
    <section>Вы уверены, что хотите удалить этот элемент?</section>
    <footer class="footer">
        <a href="" class="confirm_cancel btn">Нет</a> 
        <a href="" class="confirm_ok btn">Да</a>
    </footer>
</aside-->
<div class="modal" id="confirm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ trans('messages.delete_confirmation') }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ trans('messages.confirm_delete') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" id="delete-btn">{{ trans('messages.delete') }}</button>
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('messages.close')}}</button>
            </div>
        </div>
    </div>
</div>