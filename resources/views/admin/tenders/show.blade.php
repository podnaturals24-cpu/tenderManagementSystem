<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">{{ __('Tender Details') }}</h2>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded shadow">
      <h3 class="text-2xl font-bold">{{ $tender->name }}</h3>
      <p>Department: {{ $tender->department ?? 'N/A' }}</p>
      <p>Last date: {{ $tender->last_date ? \Carbon\Carbon::parse($tender->last_date)->format('Y-m-d') : 'N/A' }}</p>
      <p>Contact: {{ $tender->contact_person_name }} ({{ $tender->contact_person_number }})</p>
      <p>Email: {{ $tender->contact_email }}</p>

      @if($tender->document_path && \Storage::disk('public')->exists($tender->document_path))
        <a href="{{ route('tenders.download', $tender->id) }}" target="_blank" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded">
          Download Document
        </a>
      @endif

      <a href="{{ route('admin.dashboard') }}" class="inline-block mt-4 ml-2">Back</a>
    </div>
  </div>
</x-app-layout>
