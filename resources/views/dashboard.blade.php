{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
  <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

    {{-- Flash messages --}}
    @if (session('success'))
      <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded px-4 py-3">
        {{ session('success') }}
      </div>
    @endif
    @if ($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded px-4 py-3">
        <ul class="list-disc ml-5">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
      @can('register-users')
        <a href="{{ route('admin.register') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          Register User
        </a>
      @endcan
    </div>

    {{-- Stats --}}
    @php
      // If the controller provided $counts, use it; else compute from $tenders (if it's a Collection)
      $total  = $counts['total']           ?? (isset($tenders) && method_exists($tenders, 'count') ? $tenders->count() : 0);
      $firstA = $counts['approved_first']  ?? (isset($tenders) && method_exists($tenders, 'where') ? $tenders->where('status', 'approved')->count() : 0);
      $secondP= $counts['second_pending']  ?? 0;
      $thirdP = $counts['third_pending']   ?? 0;
    @endphp

    <div class="overflow-x-auto mb-6">
    <div class="flex flex-row min-w-[600px] bg-white dark:bg-gray-800 rounded shadow divide-x">
        {{-- Total Tenders --}}
        <div class="flex-1 p-4 text-center">
            <div class="font-semibold text-gray-700 dark:text-gray-300">Total Tenders</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $total }}</div>
        </div>

        {{-- First Approved --}}
        <div class="flex-1 p-4 text-center">
            <div class="font-semibold text-gray-700 dark:text-gray-300">First Approved</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $firstA }}</div>
        </div>

        {{-- Second Pending --}}
        <div class="flex-1 p-4 text-center">
            <div class="font-semibold text-gray-700 dark:text-gray-300">Second Pending</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $secondP }}</div>
        </div>

        {{-- Third Pending --}}
        <div class="flex-1 p-4 text-center">
            <div class="font-semibold text-gray-700 dark:text-gray-300">Third Pending</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $thirdP }}</div>
        </div>
    </div>
</div>


    {{-- Tabs --}}
    @php
      $active = $active ?? request('filter', 'all'); // 'all' | 'second' | 'third'
      $allUrl    = request()->fullUrlWithQuery(['filter' => 'all']);
      $secondUrl = request()->fullUrlWithQuery(['filter' => 'second']);
      $thirdUrl  = request()->fullUrlWithQuery(['filter' => 'third']);
    @endphp

    <div class="flex items-center gap-6 mb-4">
      <a href="{{ $allUrl }}"
         class="font-bold {{ $active==='all' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        All Tenders
      </a>
      {{-- You asked to name these hyperlinks “Second Stage Approved” and “Third Stage Approved”.
           Functionally, they list items pending review at that stage so you can approve/disapprove. --}}
      <a href="{{ $secondUrl }}"
         class="font-bold {{ $active==='second' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        Second Stage Approved
      </a>
      <a href="{{ $thirdUrl }}"
         class="font-bold {{ $active==='third' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        Third Stage Approved
      </a>
    </div>

    {{-- Tenders Table --}}
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
      <div class="p-4">
        <h3 class="font-bold mb-3">
          @if($active==='all') All Tenders
          @elseif($active==='second') Second Stage (Pending Review)
          @else Third Stage (Pending Review)
          @endif
        </h3>

        @if(isset($tenders) && $tenders->count())
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                  <th class="px-3 py-2 text-left">Name</th>
                  <th class="px-3 py-2 text-left">Department</th>
                  <th class="px-3 py-2 text-left">Last Date</th>
                  <th class="px-3 py-2 text-left">Expiry</th>
                  <th class="px-3 py-2 text-left">Stage</th>
                  <th class="px-3 py-2 text-left">Status</th>
                  <th class="px-3 py-2 text-left">Doc</th>
                  <th class="px-3 py-2 text-left">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tenders as $t)
                  @php
                    // Which stage are we acting on in this tab?
                    $stage = $active==='second' ? 'second' : ($active==='third' ? 'third' : 'first');

                    $lastDate  = $t->last_date
                      ? (method_exists($t->last_date, 'format') ? $t->last_date->format('Y-m-d') : \Carbon\Carbon::parse($t->last_date)->format('Y-m-d'))
                      : 'N/A';

                    $expiry    = $t->expiry_date
                      ? (method_exists($t->expiry_date, 'format') ? $t->expiry_date->format('Y-m-d') : \Carbon\Carbon::parse($t->expiry_date)->format('Y-m-d'))
                      : '—';
                  @endphp
                  <tr class="border-t">
                    <td class="px-3 py-2">
                      <a href="{{ route('admin.tenders.show', $t) }}"
                         class="text-blue-700 hover:underline"
                         target="_blank" rel="noopener">
                        {{ $t->name ?? 'N/A' }}
                      </a>
                    </td>
                    <td class="px-3 py-2">{{ $t->department ?? 'N/A' }}</td>
                    <td class="px-3 py-2">{{ $lastDate }}</td>
                    <td class="px-3 py-2">{{ $expiry }}</td>
                    <td class="px-3 py-2">
                      <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-900">
                        {{ $t->approve_stage ?? '—' }}
                      </span>
                    </td>
                    <td class="px-3 py-2">
                      @php $s = strtolower($t->status ?? ''); @endphp
                      @if($s === 'approved')
                        <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Approved</span>
                      @elseif($s === 'pending')
                        <span class="px-2 py-1 text-xs rounded bg-yellow-400 text-black">Pending</span>
                      @elseif($s === 'disapproved' || $s === 'rejected')
                        <span class="px-2 py-1 text-xs rounded bg-red-600 text-white">Disapproved</span>
                      @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-400 text-white">{{ ucfirst($t->status ?? 'N/A') }}</span>
                      @endif
                    </td>
                    <td class="px-3 py-2">
                      @if(!empty($t->document_path))
                        <a href="{{ route('tenders.download', $t) }}" class="text-blue-600 hover:underline">Download</a>
                      @else
                        <span class="text-gray-500">No File</span>
                      @endif
                    </td>
                    <td class="px-3 py-2">
                      {{-- Approve --}}
                      <form method="POST"
                            action="{{ route('admin.tenders.approve', $t) }}"
                            class="inline"
                            onsubmit="this.querySelector('button').disabled=true; return confirm('Approve this {{ $stage }} stage?');">
                        @csrf
                        <input type="hidden" name="stage" value="{{ $stage }}">
                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded">Approve</button>
                      </form>

                      {{-- Disapprove --}}
                      <form method="POST"
                            action="{{ route('admin.tenders.disapprove', $t) }}"
                            class="inline ml-2"
                            onsubmit="this.querySelector('button').disabled=true; return confirm('Disapprove this {{ $stage }} stage?');">
                        @csrf
                        <input type="hidden" name="stage" value="{{ $stage }}">
                        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded">Disapprove</button>
                      </form>

                      {{-- View --}}
                      <a href="{{ route('admin.tenders.show', $t) }}"
                         class="inline ml-2 text-sm text-blue-600 hover:underline"
                         title="View details">
                        View
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Pagination (if $tenders is a paginator) --}}
          @if(method_exists($tenders, 'links'))
            <div class="mt-4">
              {{ $tenders->links() }}
            </div>
          @endif
        @else
          <p>No tenders found.</p>
        @endif
      </div>
    </div>

    {{-- Applications --}}
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
                  @php
                    $appliedAt = $app->created_at
                      ? (method_exists($app->created_at, 'format') ? $app->created_at->format('Y-m-d H:i') : \Carbon\Carbon::parse($app->created_at)->format('Y-m-d H:i'))
                      : 'N/A';
                    $ast = strtolower($app->status ?? '');
                  @endphp
                  <tr class="border-t">
                    <td class="px-3 py-2">{{ optional($app->tender)->name ?? 'N/A' }}</td>
                    <td class="px-3 py-2">{{ optional($app->user)->name ?? 'N/A' }}</td>
                    <td class="px-3 py-2">
                      @if($ast === 'approved')
                        <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Approved</span>
                      @elseif($ast === 'pending')
                        <span class="px-2 py-1 text-xs rounded bg-yellow-400 text-black">Pending</span>
                      @elseif($ast === 'rejected' || $ast === 'disapproved')
                        <span class="px-2 py-1 text-xs rounded bg-red-600 text-white">Rejected</span>
                      @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-400 text-white">{{ ucfirst($app->status ?? 'N/A') }}</span>
                      @endif
                    </td>
                    <td class="px-3 py-2">{{ $appliedAt }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @if(method_exists($applications, 'links'))
            <div class="mt-4">
              {{ $applications->links() }}
            </div>
          @endif
        @else
          <p>No applications found.</p>
        @endif
      </div>
    </div>

  </div>
</x-app-layout>
