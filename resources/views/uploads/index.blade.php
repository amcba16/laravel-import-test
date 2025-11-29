<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CSV Upload Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Upload Form -->
                <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <input type="file" name="csv_file" required>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload CSV</button>
                </form>

                <!-- Uploaded CSVs List -->
                <h3 class="font-semibold mb-2">Uploads</h3>
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">File Name</th>
                            <th class="px-4 py-2 border">Total Products</th>
                            <th class="px-4 py-2 border">Processed</th>
                            <th class="px-4 py-2 border">Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploads as $upload)
                        <tr>
                            <td class="px-4 py-2 border">{{ $upload->file_name }}</td>
                            <td class="px-4 py-2 border">{{ $upload->total_products }}</td>
                            <td class="px-4 py-2 border">{{ $upload->processed_products }}</td>
                            <td class="px-4 py-2 border">{{ $upload->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
