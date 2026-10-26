@if ($synset_id !== null)
    <input type="hidden"
           name="synset_id"
           value="{{ $synset_id }}">
@endif

@if (!empty($examples_return))
    <input
        type="hidden"
        name="examples_return"
        value="{{ $examples_return['path'] }}"
    >
@endif