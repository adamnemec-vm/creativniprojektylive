@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand">Nový příspěvek</h2>
    </div>

    @include('admin.posts._form')
@endsection
