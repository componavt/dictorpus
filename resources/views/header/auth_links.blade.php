                        <li class="dropdown">
                    @if ($user=Sentinel::check())
                            <?php $user = User::find($user->id);?>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ $user->name() }} ({{ $user->rolesNames() }})<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                    @else
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ trans('auth.login') }}<span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/login') }}">{{ trans('auth.login') }}</a></li>
                                <li><a href="{{ url('/register') }}">{{ trans('auth.register') }}</a></li>
                                <li><a href="{{ url('/reset') }}">{{ trans('auth.reset') }}</a></li>
                            </ul>
                    @endif
                        </li>
