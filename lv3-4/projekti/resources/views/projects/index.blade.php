<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Moji projekti
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('projects.create') }}" class="inline-block bg-blue-600 text-black px-4 py-2 rounded-md hover:bg-blue-700 mb-4">Kreiraj novi projekt</a>

                    @if($projects->isEmpty())
                        <p>Nema projekata za prikaz.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naziv</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cijena</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum početka</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum završetka</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voditelj</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Članovi tima</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcije</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($projects as $project)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->description ?? 'Nema opisa' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->price ? number_format($project->price, 2) : 'Nema cijene' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d.m.Y.') : 'Nema datuma' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d.m.Y.') : 'Nema datuma' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->owner->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($project->teamMembers->isEmpty())
                                                Nema članova
                                            @else
                                                {{ $project->teamMembers->pluck('name')->join(', ') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('projects.edit', $project) }}" class="inline-block bg-yellow-500 text-black px-3 py-1 rounded-md hover:bg-yellow-600">Uredi</a>
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-black px-3 py-1 rounded-md hover:bg-red-600" onclick="return confirm('Jeste li sigurni da želite obrisati ovaj projekt?')">Obriši</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>