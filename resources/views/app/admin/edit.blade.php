@extends('layouts.app')
@section('content')
    <div class="title">
        <h2>Admin Update</h2>
    </div>
    <div class="create-page">
        <form method="POST" action="{{ route('users.update',$data['user']) }}">
            @csrf
            @method('PUT')
            <div>
                <fieldset>
                    <input id="name" type="text" name="name" value="{{ $data['user']['name'] }}" placeholder="Name" required autocomplete="name">
                    @error('name')
                        <span class="validate-error">{{ $message }}</span>
                    @enderror
                </fieldset>
                <fieldset>
                    <input id="email" type="email" name="email" value="{{ $data['user']['email'] }}" placeholder="Email" required autocomplete="email">
                    @error('email')
                        <span class="validate-error">{{ $message }}</span>
                    @enderror
                </fieldset>
                <fieldset>
                    <input id="password" type="password" name="password" placeholder="Password" autocomplete="new-password">
                    @error('password')
                        <span class="validate-error">{{ $message }}</span>
                    @enderror
                </fieldset>
                <fieldset>
                    <select name="roles" id="roles">
                        <option value="">Choose</option>
                        @foreach($data['roles'] as $key => $val)
                            <option value="{{$key}}" {{ isset($data['userRole'][$val]) ? 'selected' : ''}}>{{$val}}</option>
                        @endforeach
                    </select>
                    @error('roles')
                        <span class="validate-error">{{ $message }}</span>
                    @enderror
                </fieldset>
            </div>
            <button class="mt-20" type="submit">Submit</button>
        </form>
    </div>

@endsection

@section('js')

@endsection

@section('css')

@endsection
