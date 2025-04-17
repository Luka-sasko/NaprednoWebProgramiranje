<x-app-layout>
    <h1>{{ __('messages.add_task') }}</h1>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf

        <label>{{ __('messages.title_hr') }}</label>
        <input type="text" name="title_hr">

        <label>{{ __('messages.title_en') }}</label>
        <input type="text" name="title_en">

        <label>{{ __('messages.description') }}</label>
        <textarea name="description"></textarea>

        <label>{{ __('messages.study_type') }}</label>
        <select name="study_type">
            <option value="stručni">Stručni</option>
            <option value="preddiplomski">Preddiplomski</option>
            <option value="diplomski">Diplomski</option>
        </select>

        <button type="submit">Spremi</button>
    </form>

    <a href="{{ url('lang/hr') }}">Hrvatski</a> |
    <a href="{{ url('lang/en') }}">English</a>
</x-app-layout>