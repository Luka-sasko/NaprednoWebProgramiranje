<x-app-layout>
<h1>Prijave studenata na tvoje radove</h1>
@foreach($tasks as $task)
    <h2>{{ $task->title_hr }}</h2>
    <ul>
    @foreach($task->applications as $app)
        <li>
            Student: {{ $app->student->name }} | Prioritet: {{ $app->priority }} |
            Status: {{ $app->accepted ? 'Prihvaćen' : 'Čeka' }}
            @if(!$app->accepted && $app->priority == 1)
                <form action="{{ route('applications.accept', $app->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit">Prihvati</button>
                </form>
            @endif
        </li>
    @endforeach
    </ul>
@endforeach
</x-app-layout>