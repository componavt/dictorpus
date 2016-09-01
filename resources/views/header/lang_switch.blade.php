                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if ($localeCode != LaravelLocalization::getCurrentLocale())
                        <li>
                            <a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                                {{ $properties['native'] }}
                            </a>
                        </li>
                        @endif
                    @endforeach
