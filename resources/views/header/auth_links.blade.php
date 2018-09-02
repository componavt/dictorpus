                        <li class="dropdown">
                    @if ($user=Sentinel::check())
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
                    @else
                        <div class="user-enter">
                        {!! Form::open(['method'=>'POST', 'route'=>'login']) !!}
                            <div class="row">
                                <div class="col-sm-6">
                            @include('widgets.form._formitem_text', ['name' => 'email', 
                                                                     'attributes' => ['placeholder' => trans('auth.your_email') ]])
                                </div>
                                <div class="col-sm-6">
                            @include('widgets.form._formitem_password', ['name' => 'password', 'placeholder' => trans('auth.password') ])
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <a class="user-registr-link" href="/register">{!!trans('main.registr_link')!!}</span></a>
                                </div>
                                <div class="col-sm-6 user-enter-submit">
                                    <label><input type="checkbox" hidden><span></span></label>
                                    <span class="user-enter-remember-text">{{trans('auth.remember')}}</span>
                                    <!--label for="remember"><input name="remember" type="checkbox" hidden><span></span>
                                    {{trans('auth.remember')}}</label-->
                                    @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.login')])
                                </div>
                            </div>
                        {!! Form::close() !!}
                        </div>
                    @endif
                        </li>
