<input type="hidden" id="new_pubpart_publication_id">
<input type="hidden" id="new_pubpart_id" value="">  {{-- пустое → создаём новую pubpart;
                                                     содержит id  → редактируем существующую pubpart. --}}

<div class="form-group js-new-pubpart-non-periodic">
    <label for="new_pubpart_title">
        {{ trans('corpus.title') }}
    </label>

    <input class="form-control"
           id="new_pubpart_title"
           type="text"
           value="">
</div>

<div class="js-new-pubpart-periodic">
    <div class="form-group">
        <label for="new_pubpart_number">
            {{ trans('corpus.number') }}
        </label>

        <input class="form-control"
               id="new_pubpart_number"
               type="text"
               value="">
    </div>

    <div class="form-group">
        <label for="new_pubpart_year">
            {{ trans('messages.year') }}
        </label>

        <input class="form-control"
               id="new_pubpart_year"
               type="number"
               value="">
    </div>

    <div class="form-group">
        <label for="new_pubpart_issue_date">
            {{ trans('corpus.issue_date') }}
        </label>

        <input class="form-control"
               id="new_pubpart_issue_date"
               type="date"
               value="">
    </div>
</div>