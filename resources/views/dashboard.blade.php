<x-app-layout>
    

    {{-- Single Alpine scope for create form toggle (if you later want to keep create form for admin) --}}
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ open: false }">

        {{-- Top: Register user + stat --}}
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Create a Admin/User</h3>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Register User
                </a>

                
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-white dark:bg-gray-800 rounded shadow">
                <h3 class="font-bold">Total Tenders</h3>
                <p class="text-2xl">{{ isset($tenders) ? $tenders->count() : 0 }}</p>
            </div>
        </div>

        {{-- Optional: Create Tender Form (visible when open=true) --}}
        <div x-show="open" x-cloak class="mb-6 bg-white dark:bg-gray-800 p-6 rounded shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Create Tender</h3>
                <button type="button" @click="open = false" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">Close</button>
            </div>

            <form method="POST" action="{{ route('tenders.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block">Tender Name</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block">Department</label>
                        <input type="text" name="department" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block">Last Date</label>
                        <input type="date" name="last_date" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block">Contact Person Name</label>
                        <input type="text" name="contact_person_name" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block">Contact Number</label>
                        <input type="text" name="contact_person_number" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block">Contact Email</label>
                        <input type="email" name="contact_email" class="w-full border rounded p-2" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block">Upload Document</label>
                        <input type="file" name="document" class="w-full border rounded p-2">
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Submit Tender</button>
                    <button type="button" @click="open = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Cancel</button>
                </div>
            </form>
        </div>

        {{-- All Tenders (admin table) --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-4">
                <h3 class="font-bold mb-3">All Tenders</h3>

                @if(isset($tenders) && $tenders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Name</th>
                                    <th class="px-3 py-2 text-left">Department</th>
                                    <th class="px-3 py-2 text-left">Last Date</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenders as $tender)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <a href="{{ route('admin.tenders.show', $tender) }}" target="_blank" rel="noopener">
                                                {{ $tender->name ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="px-3 py-2">{{ $tender->department ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ $tender->last_date ? \Carbon\Carbon::parse($tender->last_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ ucfirst($tender->status ?? 'N/A') }}</td>
                                        <td class="px-3 py-2">
                                            {{-- Approve --}}
                                            <form method="POST" action="{{ route('admin.tenders.approve', $tender->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded">Approve</button>
                                            </form>

                                            {{-- Disapprove --}}
                                            <form method="POST" action="{{ route('admin.tenders.disapprove', $tender->id) }}" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded">Disapprove</button>
                                            </form>

                                            {{-- Optional: view --}}
                                            <a href="{{ route('admin.tenders.show', $tender) }}" class="inline ml-2 text-sm text-blue-600 hover:underline">View</a>
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

        {{-- Applications list (admin) --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4">
                <h3 class="font-bold mb-3">Applications</h3>

                @if(isset($applications) && $applications->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tender</th>
                                    <th class="px-3 py-2 text-left">Applicant</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Applied At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">{{ optional($app->tender)->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ optional($app->user)->name ?? 'N/A' }}</td>
                                        <td class="px-3 py-2">{{ ucfirst($app->status ?? 'N/A') }}</td>
                                        <td class="px-3 py-2">{{ $app->created_at ? \Carbon\Carbon::parse($app->created_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No applications found.</p>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
