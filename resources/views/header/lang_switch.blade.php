                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if ($localeCode != LaravelLocalization::getCurrentLocale())
                        <li>
                            <a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                            <span class="lang-sm {{$with_text ? 'lang-lbl' : ''}}" lang="{{$localeCode}}"></span>
                                {{-- $properties['native'] --}}
                            </a>
                        </li>
                        @endif
                    @endforeach
