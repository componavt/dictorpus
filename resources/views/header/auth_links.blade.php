                    @if ($user=Sentinel::check())
                        <div class="user-menu-name">
                            <?php $user = User::find($user->id);?>
                            {{ $user->name }} ({{ $user->rolesNames() }})
                        </div>
                        <div class="user-menu">
                            @if (User::checkAccess('admin'))
                            <a href="{{ url('/user') }}"><i class="fa fa-btn fa-user"></i>{{ trans('navigation.users') }}</a>
                            <a href="{{ url('/role') }}"><i class="fa fa-btn fa-users"></i>{{ trans('navigation.roles') }}</a>
                            @endif

                            <a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>{{ trans('navigation.logout') }}</a>
                        </div>                        
                    @elseif (!isset($without_enter_form) || !$without_enter_form)
                        {!! Form::open(['method'=>'POST', 'route'=>'login']) !!}
                            <div class="user-enter-input">
                            @include('widgets.form.formitem._text', ['name' => 'email', 
                                                                     'attributes' => ['placeholder' => trans('auth.your_email') ]])
                            @include('widgets.form.formitem._password', ['name' => 'password', 'placeholder' => trans('auth.password') ])
                            </div>
                            <div class="user-registr-submit">
                                <div class="user-registr-col">
                                    <a class="user-registr-link" href="/register">{!!trans('main.registr_link')!!}</span></a><br>
                                    <a class="user-registr-link" href="/reset">{!!trans('auth.reset')!!}</span></a>
                                </div>
                                <div class="remember-me">
                                    <label><input type="checkbox" hidden><span></span></label>
                                    <span class="user-enter-remember-text">{{trans('auth.remember')}}</span>
                                </div>
                                <div class="user-enter-submit">
                                    @include('widgets.form.formitem._submit', ['title' => trans('auth.login')])
                                </div>
                            </div>
                        {!! Form::close() !!}
                    @endif
