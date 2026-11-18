@if (User::checkAccess('dict.edit'))    @foreach ($collocation_example_actions[$example['example_id']] ?? [] as $action)
        <?php
        $examples_url = LaravelLocalization::localizeURL(
            '/dict/meaning/' . $source->id
            . '/collocations/' . $action['collocation_id']
            . '/examples'
        );

        $form_url = $action['attached']
            ? $examples_url . '/' . $action['attachment_id']
            : $examples_url;
        ?>

        <div class="collocation-example-action"
             data-collocation-id="{{ $action['collocation_id'] }}"
             data-examples-url="{{ $examples_url }}"
             data-attach-label="{{ trans('collocation_examples.attach') }}"
             data-attach-error="{{ trans('collocation_examples.attach_error') }}"
             data-detach-label="{{ trans('collocation_examples.detach') }}"
             data-detach-confirm="{{ trans('collocation_examples.detach_confirm') }}"
             data-detach-error="{{ trans('collocation_examples.detach_error') }}">

            <span>{{ $action['label'] }}</span>

            <span class="collocation-example-status text-success"
                  role="status"
                  @if (!$action['attached']) style="display: none;" @endif>
                {{ trans('collocation_examples.attached') }}
            </span>

            <form method="POST"
                  class="collocation-example-form{{ $action['attached'] ? ' collocation-example-detach' : '' }}"
                  action="{{ $form_url }}"
                  data-confirm="{{ $action['attached'] ? trans('collocation_examples.detach_confirm') : '' }}"
                  data-error="{{ trans($action['attached'] ? 'collocation_examples.detach_error' : 'collocation_examples.attach_error') }}">

                {!! csrf_field() !!}

                <input type="hidden" name="_method" value="DELETE"
                       @if (!$action['attached']) disabled @endif>

                <input type="hidden" name="text_id"
                       value="{{ $action['text_id'] }}">

                <input type="hidden" name="source_w_id"
                       value="{{ $action['source_w_id'] }}">

                <input type="hidden" name="partner_w_id"
                       value="{{ $action['partner_w_id'] }}">

                <button type="submit" class="btn btn-default btn-small">
                    {{ trans($action['attached'] ? 'collocation_examples.detach' : 'collocation_examples.attach') }}
                </button>

                <span class="collocation-example-error text-danger"
                      role="alert"></span>
            </form>
        </div>
    @endforeach
@endif