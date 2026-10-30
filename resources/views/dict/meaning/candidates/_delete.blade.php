<form method="POST" action="{{ $item['delete_url'] }}" class="candidate-delete"
      data-confirm="{{ trans('collocation_candidates.delete_confirm') }}"
      onsubmit="return confirm(this.getAttribute('data-confirm'))">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_method" value="DELETE">

    @foreach ($filters as $name => $value)
        @foreach ((array) $value as $entry)
            <input type="hidden" name="{{ $name }}{{ is_array($value) ? '[]' : '' }}" value="{{ $entry }}">
        @endforeach
    @endforeach

    <button type="submit" class="btn btn-link candidate-delete-button">
        {{ trans('collocations.delete') }}
    </button>
</form>