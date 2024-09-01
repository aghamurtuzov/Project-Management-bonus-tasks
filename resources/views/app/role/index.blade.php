@extends('layouts.app')
@section('content')
    <div class="title">
        <h2>Role List</h2>
        <a href="{{route('roles.create')}}" class="open-modal-btn"><button type="button">Create</button></a>
    </div>
    <div class="table-responsive">
        <table id="table-design">
            <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{$role->name}}</td>
                    <td>
                        <div class="action-buttons">
                            <a class="edit" href="{{route('roles.edit',$role)}}">Edit</a>
                            <button class="delete" type="button" onclick="event.preventDefault();
                            if (confirm('Are you sure you want to delete this item?')) {
                                     document.getElementById('delete-form').submit();
                            }">Delete</button>

                            <form id="delete-form" action="{{ route('roles.destroy',$role) }}" method="POST" class="d-none">
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











