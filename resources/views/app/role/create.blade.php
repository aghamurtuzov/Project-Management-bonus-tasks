@extends('layouts.app')
@section('content')
    <div class="title">
        <h2>Role Create</h2>
    </div>

    <div class="create-page p-30">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="flex">
                <fieldset>
                    <label for="">Name</label>
                    <input type="text" name="name" value="{{old('name')}}" required placeholder="Text"/>
                    @error('name')
                    <span class="validate-error">{{ $message }}</span>
                    @enderror
                </fieldset>
            </div>
            <div class="flex mt-20">
                @foreach($permissions as $permission)
                    <div class="col-5 col-sm-6 col-md-4 col-md-3 checkbox">
                        <input type="checkbox" id="check-{{$permission->id}}" name="permission[{{$permission->id}}]"
                               value="{{$permission->id}}"/>
                        <label class="form-check-label" for="check1">
                            {{$permission->name}}
                        </label>
                    </div>
                @endforeach
                @error('permission')
                <span class="validate-error">{{ $message }}</span>
                @enderror
            </div>
            <button class="mt-20" type="submit">Submit</button>
        </form>
    </div>

@endsection

@section('js')

@endsection

@section('css')

@endsection











