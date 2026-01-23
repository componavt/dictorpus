<div id="role{{$role_id}}" class="{{$class}}">
{!! Form::open(['method'=>'POST', 'url' => '/ru/user/remove']) !!}
    <table class="table-bordered table-wide table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th></th>
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
                <td><input type="checkbox" value="{{$user->id}}" name="to_remove[]"></td>
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
                    @if ($user->langsToString())
                    <br>{{$user->langsToString()}}
                    @endif
                </td>
                <td data-th="{{ trans('auth.last_activity') }}">
                    {{$user->last_login}}
{{-- долго грузится, переделать или грузить через ajax
                    @if ($user->getLastActionTime())
                    <br>{{$user->getLastActionTime()}}
                    @endif --}}
                </td>
                @if (User::checkAccess('user.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/user/'.$user->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'user'])
                </td>
                @endif
            </tr> 
            @endforeach
        </tbody>
    </table>
@include('widgets.form.formitem._submit', ['title' => trans('messages.delete')])
{!! Form::close() !!}
</div>

