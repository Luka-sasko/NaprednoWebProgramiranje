<x-app-layout>
<h1>Popis radova</h1>
@foreach($tasks as $task)
    <div>
        <h2>{{ $task->title_hr }}</h2>
        <p>{{ $task->description }}</p>
        <form action="{{ route('tasks.apply', $task->id) }}" method="POST">
            @csrf
            <label>Prioritet (1-5):</label>
            <input type="number" name="priority" min="1" max="5" required>
            <button type="submit">Prijavi se</button>
        </form>
    </div>
@endforeach
</x-app-layout>