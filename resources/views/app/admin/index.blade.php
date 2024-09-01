@extends('layouts.app')
@section('content')
    <div class="title">
        <h2>Admin List</h2>
        <a href="{{route('users.create')}}" class="open-modal-btn">
            <button type="button">Create</button>
        </a>
    </div>
    <div class="table-responsive">
        @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
        @endsession
        <table id="table-design">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>
                        <div class="action-buttons">
                            <a class="edit" href="{{route('users.edit',$user)}}">Edit</a>
                            <button class="delete" type="button" onclick="event.preventDefault();
                            if (confirm('Are you sure you want to delete this item?')) {
                                     document.getElementById('delete-form').submit();
                            }">Delete</button>

                            <form id="delete-form" action="{{ route('users.destroy',$user) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('js')

@endsection

@section('css')

@endsection











