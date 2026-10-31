@if ($example_collocation && User::checkAccess('dict.edit'))
    @foreach ($collocation_example_actions[$example['example_id']] ?? [] as $action)
        <div class="collocation-example-action" data-collocation-id="{{ $example_collocation->id }}">
            <span>{{ $action['label'] }}</span>

            @if ($action['attached'])
            <span class="text-success">
                ✓ {{ trans('collocation_examples.attached') }}
            </span>

            <form method="POST"
                class="collocation-example-form collocation-example-detach"
                data-confirm="{{ trans('collocation_examples.detach_confirm') }}"
                data-error="{{ trans('collocation_examples.detach_error') }}"
                action="{{ LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/collocations/' . $example_collocation->id . '/examples/' . $action['attachment_id']) }}">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">

                <button type="submit" class="btn btn-default btn-small">
                    {{ trans('collocation_examples.detach') }}
                </button>

                <span class="collocation-example-error text-danger" role="alert"></span>
            </form>
            @else
            <form method="POST" class="collocation-example-form" action="{{ LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/collocations/' . $example_collocation->id . '/examples') }}">
                {!! csrf_field() !!}

                <input type="hidden" name="text_id" value="{{ $action['text_id'] }}">
                <input type="hidden" name="source_w_id" value="{{ $action['source_w_id'] }}">
                <input type="hidden" name="partner_w_id" value="{{ $action['partner_w_id'] }}">

                <button type="submit" class="btn btn-default btn-small">
                    {{ trans('collocation_examples.attach') }}
                </button>

                <span class="collocation-example-error text-danger" role="alert"></span>
            </form>
            @endif
        </div>
    @endforeach
@endif