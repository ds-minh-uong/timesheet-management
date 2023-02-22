<x-app-layout>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Manager</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <th>{{$user->id}}</th>
                <td>{{$user->name}}</td>
                <td class="ml-3">{{$user->email}}</td>
                <td class="d-flex">
                    <form method="post" action="/manage/user/{{$user->id}}">
                        @csrf
                        @method('patch')
                        <select name="role" id="role" value={{$user->role}}>
                            <option {{$user->role === 1 ? "selected" : ""}} value="1">Admin</option>
                            <option {{$user->role === 2 ? "selected" : ""}} value="2">Manager</option>
                            <option {{$user->role === 0 ? "selected" : ""}} value="0">Employee</option>
                        </select>
                        <x-secondary-button type="submit">
                            {{ __('Change') }}
                        </x-secondary-button>
                    </form>
                </td>
                @if(isset($user->manager))
                    <td>{{$user->manager->name}}</td>
                @endif
            </tr>
        @endforeach

        </tbody>
    </table>
</x-app-layout>
