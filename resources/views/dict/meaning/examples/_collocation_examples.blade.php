@if (User::checkAccess('dict.edit'))
    @foreach ($collocation_example_actions[$example['example_id']] ?? [] as $action)
        <?php
        $examples_url = LaravelLocalization::localizeURL(
            '/dict/meaning/'.$source->id.'/collocations/'.$action['collocation_id'].'/examples'
        );
        $status = $action['status'];
        $decision_id = $action['decision_id'];
        $status_key = $status ?: 'unreviewed';
        ?>

        <div class="collocation-example-action"
             data-collocation-id="{{ $action['collocation_id'] }}"
             data-text-id="{{ $action['text_id'] }}"
             data-source-w-id="{{ $action['source_w_id'] }}"
             data-partner-w-id="{{ $action['partner_w_id'] }}"
             data-examples-url="{{ $examples_url }}"
             data-attached-label="{{ trans('collocation_examples.attached') }}"
             data-rejected-label="{{ trans('collocation_examples.rejected') }}"
             data-unreviewed-label="{{ trans('collocation_examples.unreviewed') }}">

            <span>{{ $action['label'] }}</span>

            <span class="collocation-example-status{{ $status === 'attached' ? ' text-success' : ($status === 'rejected' ? ' text-muted' : '') }}" role="status">
                {{ trans('collocation_examples.'.$status_key) }}
            </span>

            @foreach (['attached' => 'attach', 'rejected' => 'reject', 'reset' => 'reset'] as $decision_type => $label_key)
                <?php
                $is_reset = $decision_type === 'reset';
                $visible = $is_reset ? $decision_id !== null : $status !== $decision_type;
                $confirm = $decision_type === 'rejected'
                    ? trans('collocation_examples.reject_confirm')
                    : ($is_reset ? trans('collocation_examples.reset_confirm') : '');
                ?>

                <form method="POST"
                      class="collocation-example-form"
                      data-decision="{{ $decision_type }}"
                      data-confirm="{{ $confirm }}"
                      data-error="{{ trans('collocation_examples.decision_error') }}"
                      action="{{ $is_reset ? $examples_url.'/'.$decision_id : $examples_url }}"
                      style="display: {{ $visible ? 'inline-block' : 'none' }};">
                    {!! csrf_field() !!}

                    @if ($is_reset)
                        <input type="hidden" name="_method" value="DELETE">
                    @else
                        <input type="hidden" name="status" value="{{ $decision_type }}">
                    @endif

                    <input type="hidden" name="text_id" value="{{ $action['text_id'] }}">
                    <input type="hidden" name="source_w_id" value="{{ $action['source_w_id'] }}">
                    <input type="hidden" name="partner_w_id" value="{{ $action['partner_w_id'] }}">

                    <button type="submit" class="btn btn-default btn-small">
                        {{ trans('collocation_examples.'.$label_key) }}
                    </button>

                    <span class="collocation-example-error text-danger" role="alert"></span>
                </form>
            @endforeach
        </div>
    @endforeach
@endif