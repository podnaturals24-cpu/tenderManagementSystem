@extends('layouts.app')
@section('content')
<h1>Create Tender</h1>
<form method="POST" action="{{ route('tenders.store') }}" enctype="multipart/form-data">
    @csrf
    <input name="tender_id" placeholder="Tender ID"><br>
    <input name="name" placeholder="Tender Name"><br>
    <input name="department" placeholder="Department"><br>
    <input type="date" name="last_date"><br>
    <input type="file" name="document"><br>
    <input name="contact_person_name" placeholder="Contact Person"><br>
    <input name="contact_person_number" placeholder="Contact Number"><br>
    <input name="contact_email" placeholder="Contact Email"><br>
    <button type="submit">Submit</button>
</form>
@endsection
