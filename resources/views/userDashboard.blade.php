<x-app-layout>

    <div
        class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8"
        x-data="{
            open: false,          // existing: create panel
            editOpen: false,      // existing: approved-edit panel toggle

            // ✅ NEW: second-stage edit panel toggle + form
            secondStageEditOpen: false,
            secondStageForm: {
                id: null,
                details_of_emd: '',
                emd_number: '',
                emd_date: '',
                expiry_date: '',
            },

            form: {               // existing: approved-edit form data
                id: null,
                name: '', department: '',
                last_date: '', expiry_date: '',
                pre_bid_date: '',
                value_of_tender: '',
                contact_person_name: '',
                contact_person_number: '',
                contact_email: '',
                // NEW columns (existing in your code)
                tech_criteria: '',
                tech_eligibilty: '',
                fin_criteria: '',
                fin_elegibility: '',
                tender_doucemnt_uploaded_date: '',
            },

            openEdit(t) {         // existing: open approved-edit with prefilled data
                console.log('openEdit called with:', t);
                this.form.id = t.id
                this.form.name = t.name ?? ''
                this.form.department = t.department ?? ''
                this.form.last_date = t.last_date ?? ''
                this.form.expiry_date = t.expiry_date ?? ''
                this.form.pre_bid_date = (t.pre_bid_date ?? '').toString().replace(' ', 'T')
                this.form.value_of_tender = t.value_of_tender ?? ''
                this.form.contact_person_name = t.contact_person_name ?? ''
                this.form.contact_person_number = t.contact_person_number ?? ''
                this.form.contact_email = t.contact_email ?? ''
                this.form.tech_criteria = t.tech_criteria ?? ''
                this.form.tech_eligibilty = t.tech_eligibilty ?? ''
                this.form.fin_criteria = t.fin_criteria ?? ''
                this.form.fin_elegibility = t.fin_elegibility ?? ''
                this.form.tender_doucemnt_uploaded_date = (t.tender_doucemnt_uploaded_date ?? '').toString().replace(' ', 'T')

                // open approved-edit, close others
                this.editOpen = true
                this.secondStageEditOpen = false
                this.open = false
                $nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))
            },

            // ✅ NEW: open second-stage edit with only EMD fields
            openSecondStageEdit(t) {
                this.secondStageForm.id = t.id
                this.secondStageForm.details_of_emd = t.details_of_emd ?? ''
                this.secondStageForm.emd_number = t.emd_number ?? ''
                // inputs type=date need YYYY-MM-DD
                this.secondStageForm.emd_date = (t.emd_date ?? '').toString().slice(0, 10)
                this.secondStageForm.expiry_date = (t.expiry_date ?? '').toString().slice(0, 10)

                // open second-stage panel, close others
                this.secondStageEditOpen = true
                this.editOpen = false
                this.open = false
                $nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))
            }
        }"
    >

        {{-- Create Tender Panel --}}
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Create a Tender</h3>

                {{-- Show Create button only when form is closed --}}
                <button
                    x-show="!open"
                    x-cloak
                    @click="open = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >
                    Create
                </button>
            </div>

            {{-- Tender Form (hidden until Create is clicked) --}}
            <div x-show="open" x-cloak class="mt-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
                <form method="POST" action="{{ route('tenders.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block">Tender Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block">Department</label>
                            <input type="text" name="department" value="{{ old('department') }}" class="w-full border rounded p-2" required>
                            @error('department') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block">Last Date</label>
                            <input type="date" name="last_date" value="{{ old('last_date') }}" class="w-full border rounded p-2" required>
                            @error('last_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ✅ Expiry Date --}}
                        <div>
                            <label class="block">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full border rounded p-2" required>
                            @error('expiry_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ✅ Pre-Bid Date --}}
                        <div>
                            <label class="block">Pre-Bid Date</label>
                            <input type="datetime-local" name="pre_bid_date" value="{{ old('pre_bid_date') }}" class="w-full border rounded p-2" required>
                            @error('pre_bid_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ✅ Value of Tender --}}
                        <div>
                            <label class="block">Value of Tender</label>
                            <textarea name="value_of_tender" rows="3" class="w-full border rounded p-2" required>{{ old('value_of_tender') }}</textarea>
                            @error('value_of_tender') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block">Contact Person Name</label>
                            <input type="text" name="contact_person_name" value="{{ old('contact_person_name') }}" class="w-full border rounded p-2" required>
                            @error('contact_person_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block">Contact Number</label>
                            <input type="text" name="contact_person_number" value="{{ old('contact_person_number') }}" class="w-full border rounded p-2" required>
                            @error('contact_person_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email') }}" class="w-full border rounded p-2" required>
                            @error('contact_email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block">Upload Document</label>
                            <input type="file" name="document" class="w-full border rounded p-2">
                            @error('document') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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

        {{-- ✅ Edit Tender Panel (existing Approved edit) --}}
        <div class="mb-6" x-show="editOpen" x-cloak>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Edit Tender</h3>
                <button
                    @click="editOpen = false"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded"
                >
                    Close
                </button>
            </div>

            <div class="mt-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
                <form method="POST"
                      :action="'{{ url('/tenders') }}/' + form.id + '/update'"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        {{-- Readonly fields --}}
                        <div>
                            <label class="block">Tender Name</label>
                            <input type="text" name="name" x-model="form.name" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Department</label>
                            <input type="text" name="department" x-model="form.department" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Last Date</label>
                            <input type="date" name="last_date" x-model="form.last_date" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Expiry Date</label>
                            <input type="date" name="expiry_date" x-model="form.expiry_date" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Pre-Bid Date</label>
                            <input type="datetime-local" name="pre_bid_date" x-model="form.pre_bid_date" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Value of Tender</label>
                            <textarea name="value_of_tender" rows="3" x-model="form.value_of_tender" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly></textarea>
                        </div>
                        <div>
                            <label class="block">Contact Person Name</label>
                            <input type="text" name="contact_person_name" x-model="form.contact_person_name" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Contact Number</label>
                            <input type="text" name="contact_person_number" x-model="form.contact_person_number" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block">Contact Email</label>
                            <input type="email" name="contact_email" x-model="form.contact_email" class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
                        </div>

                        {{-- Optional new document upload --}}
                        <div>
                            <label class="block">Upload New Document (optional)</label>
                            <input type="file" name="document" class="w-full border rounded p-2">
                        </div>

                        {{-- Editable new fields --}}
                        <div>
                            <label class="block">Technical Criteria</label>
                            <textarea name="tech_criteria" rows="4" x-model="form.tech_criteria" class="w-full border rounded p-2"></textarea>
                        </div>
                        <div>
                            <label class="block">Technical Eligibility</label>
                            <textarea name="tech_eligibilty" rows="4" x-model="form.tech_eligibilty" class="w-full border rounded p-2"></textarea>
                        </div>
                        <div>
                            <label class="block">Financial Criteria</label>
                            <textarea name="fin_criteria" rows="4" x-model="form.fin_criteria" class="w-full border rounded p-2"></textarea>
                        </div>

                        {{-- Changed Financial Eligibility to Yes/No dropdown --}}
                        <div>
                            <label class="block">Financial Eligibility</label>
                            <select name="fin_elegibility" x-model="form.fin_elegibility" class="w-full border rounded p-2">
                                <option value="">Select</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <div>
                            <label class="block">Tender Document Uploaded Date</label>
                            <input type="datetime-local" name="tender_doucemnt_uploaded_date" x-model="form.tender_doucemnt_uploaded_date" class="w-full border rounded p-2">
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Update Tender
                        </button>
                        <button type="button" @click="editOpen = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ✅ Second-Stage EMD Edit Panel (only 4 fields) --}}
        <div class="mb-6" x-show="secondStageEditOpen" x-cloak>
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Edit EMD (Second Stage)</h3>
                <button
                    @click="secondStageEditOpen = false"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded"
                >
                    Close
                </button>
            </div>

            <div class="mt-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
                <form method="POST"
                      {{-- ✅ uses POST /tenders/{id}/updateStage (tenders.updateDataStage) --}}
                      :action="`{{ url('/tenders') }}/${secondStageForm.id}/updateStage`">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block">Details of EMD</label>
                            <textarea name="details_of_emd" rows="5" x-model="secondStageForm.details_of_emd"
                                      class="w-full border rounded p-2"></textarea>
                        </div>

                        <div>
                            <label class="block">EMD Number</label>
                            <input type="number" name="emd_number" x-model="secondStageForm.emd_number"
                                   class="w-full border rounded p-2" inputmode="numeric">
                        </div>

                        <div>
                            <label class="block">EMD Date</label>
                            <input type="date" name="emd_date" x-model="secondStageForm.emd_date"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block">Expiry Date</label>
                            <input type="date" name="expiry_date" x-model="secondStageForm.expiry_date"
                                   class="w-full border rounded p-2">
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Update EMD
                        </button>
                        <button type="button" @click="secondStageEditOpen = false" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- WRAP: hide the lists when Create is open --}}
        <div x-show="!open" x-cloak>

            {{-- If there are any tenders -> show All/Approved/Second-Stage switchable --}}
            @if(isset($tenders) && $tenders->count())

                @php
                    // Active tab from query: 'all' (default), 'approved', 'second_stage_approved'
                    $active = request('filter', 'all');

                    // Build URLs preserving other query params
                    $allUrl = request()->fullUrlWithQuery(['filter' => null]);
                    $approvedUrl = request()->fullUrlWithQuery(['filter' => 'approved']);
                    $secondStageUrl = request()->fullUrlWithQuery(['filter' => 'second_stage_approved']);

                    // Filter the collection in Blade (client-side). For large lists, move to controller.
                    switch ($active) {
                        case 'approved':
                            $filteredTenders = $tenders->where('status', 'approved')->values();
                            break;
                        case 'second_stage_approved':
                            $filteredTenders = $tenders->where('approve_stage', 'second_stage_approved')->values();
                            break;
                        default:
                            $filteredTenders = $tenders;
                    }

                    // Labels
                    $labelMap = [
                        'all' => 'All Tenders',
                        'approved' => 'Approved Tenders',
                        'second_stage_approved' => 'Second Stage Approved',
                    ];
                    $activeLabel = $labelMap[$active] ?? 'All Tenders';
                @endphp

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        {{-- Tabs header --}}
                        <div class="flex items-center gap-4 justify-between mb-4">
                            <a href="{{ $allUrl }}"
                               class="text-lg font-bold {{ $active === 'all' ? 'text-blue-600 underline' : 'text-gray-700 hover:text-blue-600' }}">
                                All Tenders
                            </a>
                            <a href="{{ $approvedUrl }}"
                               class="text-lg font-bold {{ $active === 'approved' ? 'text-blue-600 underline' : 'text-gray-700 hover:text-blue-600' }}">
                                Approved Tenders
                            </a>
                            <a href="{{ $secondStageUrl }}"
                               class="text-lg font-bold {{ $active === 'second_stage_approved' ? 'text-blue-600 underline' : 'text-gray-700 hover:text-blue-600' }}">
                                Second Stage Approved
                            </a>
                        </div>

                        <p class="mb-4">
                            <span class="font-semibold">{{ $activeLabel }}</span>
                            — Total: {{ $filteredTenders->count() }}
                        </p>

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
                                        @if($active === 'approved' || $active === 'second_stage_approved')
                                            {{-- ✅ Actions column for Approved & Second Stage Approved --}}
                                            <th class="px-4 py-2 border">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($filteredTenders as $tender)
                                        <tr>
                                            <td class="px-4 py-2 border">{{ $tender->name }}</td>
                                            <td class="px-4 py-2 border">{{ $tender->department }}</td>
                                            <td class="px-4 py-2 border">
                                                {{ $tender->last_date ? \Carbon\Carbon::parse($tender->last_date)->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 border">{{ $tender->contact_person_name }}</td>
                                            <td class="px-4 py-2 border">{{ ucfirst($tender->status ?? 'N/A') }}</td>
                                            <td class="px-4 py-2 border">
                                                @if(!empty($tender->document_path))
                                                    <a href="{{ route('tenders.download', $tender->id) }}" class="text-blue-600 hover:underline">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>

                                            @if($active === 'approved' || $active === 'second_stage_approved')
                                                {{-- ✅ Actions cell --}}
                                                <td class="px-4 py-2 border">
                                                    @if($active === 'approved')
                                                        {{-- existing Approved edit --}}
                                                        <button
                                                            type="button"
                                                            class="text-blue-600 hover:underline"
                                                            @click="openEdit(@js([
                                                                'id' => $tender->id,
                                                                'name' => $tender->name,
                                                                'department' => $tender->department,
                                                                'last_date' => $tender->last_date,
                                                                'expiry_date' => $tender->expiry_date ?? null,
                                                                'pre_bid_date' => $tender->pre_bid_date,
                                                                'value_of_tender' => $tender->value_of_tender,
                                                                'contact_person_name' => $tender->contact_person_name,
                                                                'contact_person_number' => $tender->contact_person_number,
                                                                'contact_email' => $tender->contact_email,
                                                                'tech_criteria' => $tender->tech_criteria,
                                                                'tech_eligibilty' => $tender->tech_eligibilty,
                                                                'fin_criteria' => $tender->fin_criteria,
                                                                'fin_elegibility' => $tender->fin_elegibility,
                                                                'tender_doucemnt_uploaded_date' => $tender->tender_doucemnt_uploaded_date,
                                                            ]))"
                                                        >
                                                            Edit
                                                        </button>
                                                    @else
                                                        {{-- ✅ Second Stage Approved edit (only EMD fields) --}}
                                                        <button
                                                            type="button"
                                                            class="text-blue-600 hover:underline"
                                                            @click="openSecondStageEdit(@js([
                                                                'id' => $tender->id,
                                                                'details_of_emd' => $tender->details_of_emd,
                                                                'emd_number' => $tender->emd_number,
                                                                'emd_date' => $tender->emd_date,
                                                                'expiry_date' => $tender->expiry_date,
                                                            ]))"
                                                        >
                                                            Edit
                                                        </button>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $active === 'approved' || $active === 'second_stage_approved' ? '7' : '6' }}" class="px-4 py-4 text-center text-gray-600">
                                                No tenders found for this filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            {{-- Else (no tenders) -> show My Tender Applications --}}
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">My Tender Applications</h3>

                        @if(isset($applications) && $applications->count())
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
                                                <td class="px-4 py-2 border">{{ optional($app->tender)->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 border">{{ optional($app->tender)->department ?? 'N/A' }}</td>
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
                                                    {{ optional($app->tender->last_date) ? \Carbon\Carbon::parse($app->tender->last_date)->format('Y-m-d') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>You haven’t applied for any tenders yet.</p>
                        @endif

                    </div>
                </div>
            @endif

        </div> {{-- end x-show="!open" wrapper --}}

    </div> {{-- end top-level x-data --}}

</x-app-layout>
