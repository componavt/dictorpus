                    @if ($user=Sentinel::check())
                        <li class="dropdown">
                            <?php $user = User::find($user->id);?>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ $user->name }} ({{ $user->rolesNames() }})<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if (User::checkAccess('admin'))
                                <li><a href="{{ url('/user') }}"><i class="fa fa-btn fa-user"></i>{{ trans('navigation.users') }}</a></li>
                                <li><a href="{{ url('/role') }}"><i class="fa fa-btn fa-users"></i>{{ trans('navigation.roles') }}</a></li>
                                @endif
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>{{ trans('navigation.logout') }}</a></li>
                            </ul>
                        </li>
                    @else
                        {!! Form::open(['method'=>'POST', 'route'=>'login']) !!}
                            <div class="user-enter-input">
                            @include('widgets.form._formitem_text', ['name' => 'email', 
                                                                     'attributes' => ['placeholder' => trans('auth.your_email') ]])
                            @include('widgets.form._formitem_password', ['name' => 'password', 'placeholder' => trans('auth.password') ])
                            </div>
                            <div class="user-registr-submit">
                                <div class="user-registr-col">
                                    <a class="user-registr-link" href="/register">{!!trans('main.registr_link')!!}</span></a>
                                </div>
                                <div class="remember-me">
                                    <label><input type="checkbox" hidden><span></span></label>
                                    <span class="user-enter-remember-text">{{trans('auth.remember')}}</span>
                                </div>
                                <div class="user-enter-submit">
                                    @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.login')])
                                </div>
                            </div>
                        {!! Form::close() !!}
                    @endif
