<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('All Tenders') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                <h3 class="text-lg font-bold mb-4">Tender List</h3>

                <p class="mb-4">Total tenders: {{ $tenders->count() }}</p>

                @if($tenders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 dark:border-gray-700 text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    <th class="px-4 py-2 border">Name</th>
                                    <th class="px-4 py-2 border">Department</th>
                                    <th class="px-4 py-2 border">Last Date</th>
                                    <th class="px-4 py-2 border">Contact Person</th>
                                    <th class="px-4 py-2 border">Status</th>
                                    <th class="px-4 py-2 border">Document</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenders as $tender)
                                    <tr>
                                        <td class="px-4 py-2 border">{{ $tender->name }}</td>
                                        <td class="px-4 py-2 border">{{ $tender->department }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $tender->last_date ? \Carbon\Carbon::parse($tender->last_date)->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 border">{{ $tender->contact_person_name }}</td>
                                        <td class="px-4 py-2 border">
                                            @if($tender->status == 'pending')
                                                <span class="px-2 py-1 text-xs rounded bg-yellow-400 text-black">Pending</span>
                                            @elseif($tender->status == 'approved')
                                                <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Approved</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded bg-red-500 text-white">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border">
                                            @if($tender->document_path)
                                                <a href="{{ route('tenders.download', $tender->id) }}" 
                                                   class="text-blue-600 hover:underline">Download</a>
                                            @else
                                                No File
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No tenders found.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
