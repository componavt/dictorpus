<div id="role{{$role_id}}" class="{{$class}}">
    <table class="table-bordered table-wide table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>No</th>
                <th>E-mail</th>
                <th>{{ trans('auth.name') }}</th>
                <th>{{ trans('auth.city') }} / {{ trans('auth.affilation') }}</th>
                <th>{{ trans('auth.roles') }} / {{ trans('navigation.langs') }}</th>
                <th>{{ trans('auth.last_activity') }}</th>
                @if (User::checkAccess('user.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($users[$role_id] as $user)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="E-mail">{{$user->email}}</td>
                <td data-th="{{ trans('auth.name') }}">{{$user->first_name}} {{$user->last_name}}</td>
                <td data-th="{{ trans('auth.city') }} / {{ trans('auth.affilation') }}">
                    {{$user->country}}@if ($user->city)
                    , {{$user->city}}
                    @endif
                    @if ($user->affilation)
                    , {{$user->affilation}}
                    @endif
                </td>
                <td data-th="{{ trans('auth.roles') }} / {{ trans('navigation.langs') }}">
                    {{$user->rolesNames()}}
                    @if ($user->langString())
                    <br>{{$user->langString()}}
                    @endif
                </td>
                <td data-th="{{ trans('auth.last_activity') }}">
                    {{$user->last_login}}
                    @if ($user->getLastActionTime())
                    <br>{{$user->getLastActionTime()}}
                    @endif
                </td>
                @if (User::checkAccess('user.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/user/'.$user->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'user.destroy', 
                              'args'=>['id' => $user->id]
                             ])
                </td>
                @endif
            </tr> 
            @endforeach
        </tbody>
    </table>
</div>