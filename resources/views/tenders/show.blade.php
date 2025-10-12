@extends('layouts.app')
@section('content')
<h1>{{ $tender->name }}</h1>
<p>ID: {{ $tender->tender_id }}</p>
<p>Status: {{ $tender->status }}</p>
@if($tender->document_path)
    <a href="{{ route('tenders.download',$tender) }}">Download Document</a>
@endif
<form method="POST" action="{{ route('tenders.apply',$tender) }}">
    @csrf
    <textarea name="notes" placeholder="Notes"></textarea><br>
    <button type="submit">Apply</button>
</form>
<h3>Applications</h3>
@foreach($tender->applications as $app)
    <p>{{ $app->notes }} - {{ $app->status }}</p>
@endforeach
@endsection
