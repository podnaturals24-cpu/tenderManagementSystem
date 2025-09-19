@extends('layouts.app')
@section('content')
<h1>{{ $tender->name }}</h1>
<p>Status: {{ $tender->status }}</p>
<form method="POST" action="{{ route('admin.tenders.approve',$tender) }}">
    @csrf
    <button type="submit">Approve</button>
</form>
<form method="POST" action="{{ route('admin.tenders.disapprove',$tender) }}">
    @csrf
    <button type="submit">Disapprove</button>
</form>

<h3>Applications</h3>
@foreach($tender->applications as $app)
    <p>{{ $app->notes }} - {{ $app->status }}</p>
    <form method="POST" action="{{ route('applications.updateStatus',$app) }}">
        @csrf
        <select name="status">
            <option value="approved">Approve</option>
            <option value="rejected">Reject</option>
        </select>
        <button type="submit">Update</button>
    </form>
@endforeach
@endsection
