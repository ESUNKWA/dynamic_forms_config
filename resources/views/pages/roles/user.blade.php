
<table>
    <thead>
        <tr>
            <th width="50%" >Roles</th>
            <th width="50%" >Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach ( $users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>
                <a href="{{ $user->id }}" title="{{ $user->id }}">Voir</a>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
