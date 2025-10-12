<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded shadow">
      <form method="POST" action="{{ route('admin.register.store') }}">
        @csrf

        <div class="mb-4">
          <label class="block">Name</label>
          <input name="name" type="text" required class="w-full border rounded p-2" value="{{ old('name') }}">
          <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="mb-4">
          <label class="block">Email</label>
          <input name="email" type="email" required class="w-full border rounded p-2" value="{{ old('email') }}">
          <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="mb-4">
          <label class="block">Password</label>
          <input name="password" type="password" required class="w-full border rounded p-2">
          <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="mb-4">
          <label class="block">Confirm Password</label>
          <input name="password_confirmation" type="password" required class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
          <label class="block">Role</label>
          <select name="role" required class="w-full border rounded p-2">
            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
          <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <div class="flex gap-2">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create User</button>
          <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 px-4 py-2 rounded">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
