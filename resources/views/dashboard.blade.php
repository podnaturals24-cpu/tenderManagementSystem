{{-- resources/views/admin/dashboard.blade.php --}}
<x-app-layout>
  <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

    {{-- Flash / Errors --}}
    @if (session('success'))
      <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded px-4 py-3">
        {{ session('success') }}
      </div>
    @endif
    @if (session('info'))
      <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded px-4 py-3">
        {{ session('info') }}
      </div>
    @endif
    @if (session('error'))
      <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded px-4 py-3">
        {{ session('error') }}
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
      $total   = $counts['total']          ?? 0;
      $firstA  = $counts['approved_first'] ?? 0;
      $secondP = $counts['second_pending'] ?? 0;
      $thirdP  = $counts['third_pending']  ?? 0;

      $active = $active ?? request('filter', 'all'); // 'all' | 'second' | 'third'
      $allUrl    = request()->fullUrlWithQuery(['filter' => 'all']);
      $secondUrl = request()->fullUrlWithQuery(['filter' => 'second']);
      $thirdUrl  = request()->fullUrlWithQuery(['filter' => 'third']);
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
          <a href="{{ $secondUrl }}" class="text-2xl font-bold text-blue-700 hover:underline">{{ $secondP }}</a>
        </div>
        {{-- Third Pending --}}
        <div class="flex-1 p-4 text-center">
          <div class="font-semibold text-gray-700 dark:text-gray-300">Third Pending</div>
          <a href="{{ $thirdUrl }}" class="text-2xl font-bold text-blue-700 hover:underline">{{ $thirdP }}</a>
        </div>
      </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-6 mb-4">
      <a href="{{ $allUrl }}"
         class="font-bold {{ $active==='all' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        All Tenders
      </a>
      <a href="{{ $secondUrl }}"
         class="font-bold {{ $active==='second' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        Second Stage Pending
      </a>
      <a href="{{ $thirdUrl }}"
         class="font-bold {{ $active==='third' ? 'text-blue-600 underline' : 'hover:text-blue-600' }}">
        Third Stage Pending
      </a>
    </div>

    {{-- Tenders Table --}}
    @php
      // Stage value to post with forms in this tab
      $stageForForm = $active==='second' ? 'second' : ($active==='third' ? 'third' : 'first');

      // Helpers
      $fmtDate = function($d, $fallback = '—') {
        if (!$d) return $fallback;
        try {
          return method_exists($d, 'format')
              ? $d->format('Y-m-d')
              : \Carbon\Carbon::parse($d)->format('Y-m-d');
        } catch (\Throwable $e) {
          return $fallback;
        }
      };
    @endphp

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
                    $lastDate = $fmtDate($t->last_date, 'N/A');
                    $expiry   = $fmtDate($t->expiry_date, '—');
                    $status   = strtolower($t->status ?? '');
                    $stageVal = $t->approve_stage ?? '—';
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
                        {{ $stageVal }}
                      </span>
                    </td>
                    <td class="px-3 py-2">
                      @if($status === 'approved')
                        <span class="px-2 py-1 text-xs rounded bg-green-500 text-white">Approved</span>
                      @elseif($status === 'pending' || $status === '')
                        <span class="px-2 py-1 text-xs rounded bg-yellow-400 text-black">Pending</span>
                      @elseif($status === 'disapproved' || $status === 'rejected')
                        <span class="px-2 py-1 text-xs rounded bg-red-600 text-white">Disapproved</span>
                      @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-400 text-white">{{ ucfirst($t->status ?? 'N/A') }}</span>
                      @endif
                    </td>
                    <td class="px-3 py-2">
                      @if(!empty($t->document_path))
                        <a href="{{ route('admin.tenders.download', $t) }}" class="text-blue-600 hover:underline">Download</a>
                      @else
                        <span class="text-gray-500">No File</span>
                      @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      {{-- Approve --}}
                      <form method="POST"
                            action="{{ route('admin.tenders.approve', $t) }}"
                            class="inline"
                            onsubmit="this.querySelector('button').disabled=true; return confirm('Approve this {{ $stageForForm }} stage?');">
                        @csrf
                        <input type="hidden" name="stage" value="{{ $stageForForm }}">
                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded">Approve</button>
                      </form>

                      {{-- Disapprove --}}
                      <form method="POST"
                            action="{{ route('admin.tenders.disapprove', $t) }}"
                            class="inline ml-2"
                            onsubmit="this.querySelector('button').disabled=true; return confirm('Disapprove this {{ $stageForForm }} stage?');">
                        @csrf
                        <input type="hidden" name="stage" value="{{ $stageForForm }}">
                        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded">Disapprove</button>
                      </form>

                      <!-- {{-- View --}}
                      <a href="{{ route('admin.tenders.show', $t) }}"
                         class="inline ml-2 text-sm text-blue-600 hover:underline"
                         title="View details">
                        View
                      </a> -->
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Pagination (LengthAwarePaginator / Paginator) --}}
          @php
            $isPaginator = $tenders instanceof \Illuminate\Contracts\Pagination\Paginator
                        || $tenders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
          @endphp
          @if($isPaginator)
            <div class="mt-4">
              {{ $tenders->links() }}
            </div>
          @endif
        @else
        <p class="text-gray-600">
    @if($active==='second')
      No tenders in Second Stage Pending.
    @elseif($active==='third')
      No tenders in Third Stage Pending.
    @else
      No tenders found.
    @endif
  </p>        @endif
      </div>
    </div>

  

  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @if (session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: @json(session('success')),
      confirmButtonText: 'OK'
    }).then(() => {
      // After OK, send them to the dashboard (or stay on the same page if you prefer)
      window.location.href = "{{ route('dashboard') }}";
    });
  </script>
@endif
@if (session('error'))
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Action not allowed',
      text: @json(session('error')),
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = "{{ route('dashboard') }}";
    });
  </script>
@endif


</x-app-layout>