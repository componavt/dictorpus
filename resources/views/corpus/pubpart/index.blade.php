<div
    id="publication-pubparts-show"
    data-publication-id="{{ $publication->id }}"
    data-is-periodic="{{ $publication->is_periodic ? 1 : 0 }}"
>
    <p>
        <b>{{ trans('corpus.pubparts') }}:</b>

        @if (User::checkAccess('ref.edit'))
            <button
                type="button"
                id="create-publication-pubpart"
                class="btn btn-link"
                title="{{ trans('messages.create_new_f') }}"
                aria-label="{{ trans('messages.create_new_f') }}"
            >
                <i class="fa fa-plus fa-lg"></i>
            </button>
        @endif
    </p>

    @if ($publication->pubparts->count())
        <div class="topic-list">
            @foreach ($publication->pubparts as $pubpart)
                @php
                    $texts_count = $pubpart->texts()->count();
                @endphp

                <div
                    class="publication-pubpart"
                    data-pubpart-id="{{ $pubpart->id }}"
                >
                    <span>
                        {{ $pubpart->full_name }}
                    </span>

                    <span>
                        (
                        @if ($texts_count)
                            <a href="{{ route('text.index', [
                                'search_pubpart' => $pubpart
                            ]) }}">
                                {{ $texts_count }}
                            </a>
                        @else
                            0
                        @endif
                        )
                    </span>

                    @if (User::checkAccess('ref.edit'))
                        <button
                            type="button"
                            class="edit-publication-pubpart btn btn-link"
                            data-pubpart-id="{{ $pubpart->id }}"
                            data-title="{{ $pubpart->title }}"
                            data-number="{{ $pubpart->number }}"
                            data-year="{{ $pubpart->year }}"
                            data-issue-date="{{ $pubpart->issue_date }}"
                            title="{{ trans('messages.edit') }}"
                            aria-label="{{ trans('messages.edit') }}"
                        >
                            <i class="fa fa-pencil"></i>
                        </button>

                        <button
                            type="button"
                            class="delete-publication-pubpart btn btn-link"
                            data-pubpart-id="{{ $pubpart->id }}"
                            data-pubpart-name="{{ $pubpart->full_name }}"
                            title="{{ trans('messages.delete') }}"
                            aria-label="{{ trans('messages.delete') }}"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

@include('widgets.modal', [
    'name' => 'modalAddPubpart',
    'title' => trans('corpus.add_pubpart'),
    'submit_id' => 'save-pubpart',
    'submit_onClick' => 'savePubpartFromModal()',
    'submit_title' => trans('messages.save'),
    'modal_view' => 'corpus.pubpart._form_simple_create'
])
