<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ open: false }">

        {{-- Create Tender Panel --}}
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Create a Tender</h3>
                {{-- ✅ Show Create button only when form is closed --}}
                <button 
                    @click="open = true" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                    x-show="!open"
                >
                    Create
                </button>
            </div>

            {{-- Tender Form --}}
            <div x-show="open" x-cloak class="mt-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
                <form method="POST" action="{{ route('tenders.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block">Tender Name</label>
                            <input type="text" name="name" class="w-full border rounded p-2" required>
                        </div>
                        <div>
                            <label class="block">Department</label>
                            <input type="text" name="department" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block">Last Date</label>
                            <input type="date" name="last_date" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block">Contact Person Name</label>
                            <input type="text" name="contact_person_name" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block">Contact Number</label>
                            <input type="text" name="contact_person_number" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block">Contact Email</label>
                            <input type="email" name="contact_email" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block">Upload Document</label>
                            <input type="file" name="document" class="w-full border rounded p-2">
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Submit Tender
                        </button>
                        <button type="button" @click="open = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Applications or All Tenders --}}
        <div x-show="!open" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if($applications->count())
                    {{-- My Applications --}}
                    <h3 class="text-lg font-bold mb-4">My Tender Applications</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 dark:border-gray-700 text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    <th class="px-4 py-2 border">Tender Name</th>
                                    <th class="px-4 py-2 border">Department</th>
                                    <th class="px-4 py-2 border">Status</th>
                                    <th class="px-4 py-2 border">Last Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr>
                                        <td class="px-4 py-2 border">{{ $app->tender->name }}</td>
                                        <td class="px-4 py-2 border">{{ $app->tender->department }}</td>
                                        <td class="px-4 py-2 border">
                                            @if($app->status == 'pending')
                                                <span class="px-2 py-1 text-xs rounded bg-yellow-400 text-black">Pending</span>
                                            @elseif($app->status == 'approved')
                                                <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Approved</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded bg-red-500 text-white">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 border">
                                            {{ optional($app->tender->last_date)->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    {{-- All Tenders --}}
                    <h3 class="text-lg font-bold mb-4">All Tenders</h3>
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
                                                    <a href="{{ route('tenders.download', $tender->id) }}" class="text-blue-600 hover:underline">Download</a>
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
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
