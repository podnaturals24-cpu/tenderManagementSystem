@extends('layouts.app')
@section('content')
<h1>Admin - All Tenders</h1>
@foreach($tenders as $t)
    <p><a href="{{ route('admin.tenders.show',$t) }}">{{ $t->name }}</a> - {{ $t->status }}</p>
@endforeach
@endsection
