@extends('layouts.page')

@section('page_title')
{{ trans('navigation.collections') }}
@stop

@section('headExtra')
    {!! css('text') !!}
    
@stop

@section('body')
    <div class="row">
        <div class="col-sm-8">
            <h2>{{ $collection->getName() }}</h2>

            @if (trim(trans('collection.about.' . $id)) !== '')
                <div class="collection-annotation">
                    {!! trans('collection.about.' . $id) !!}
                </div>
            @endif

            <p>
                {{ trans('collection.total_count') }}:
                {{ $text_count }}
            </p>
        </div>

        <div class="col-sm-4" style="text-align: right">
            @include('corpus.publication.photo')
        </div>
    </div>

    @if ($pubparts_by_year->isEmpty())
        <p>{{ trans('collection.publication_empty') }}</p>
    @else
        @foreach ($pubparts_by_year as $year => $pubparts)
            <section class="collection-year">
                <h2>
                    @if ($year)
                        {{ $year }}
                    @else
                        {{ trans('collection.publication_no_year') }}
                    @endif
                </h2>

                @foreach ($pubparts as $pubpart)
                    <section
                        class="collection-issue"
                        id="issue-{{ $pubpart->id }}"
                    >
                        <h3>
                            @if ($pubpart->number)
                                № {{ $pubpart->number }}
                            @else
                                {{ $pubpart->title }}
                            @endif

                            @if ($pubpart->issue_date > '0000-00-00')
                                <small>
                                    ({{ $pubpart->issue_date }})
                                </small>
                            @endif
                        </h3>

                        @if ($pubpart->texts->isEmpty())
                            <p class="text-muted">
                                {{ trans('collection.publication_empty_issue') }}
                            </p>
                        @else
                            <ul class="collection-articles">
                                @foreach ($pubpart->texts as $text)
                                    <li>
                                        <a href="{{ LaravelLocalization::localizeURL('/corpus/text/' . $text->id) }}">
                                            {{ $text->title }}
                                        </a>

                                        @if (
                                            $text->pivot->pages !== null
                                            && $text->pivot->pages !== ''
                                        )
                                            <span class="text-muted">
                                                —
                                                {{ trans('collection.publication_pages') }}
                                                {{ $text->pivot->pages }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                @endforeach
            </section>
        @endforeach
    @endif
@stop