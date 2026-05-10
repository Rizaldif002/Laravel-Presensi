@php
    $except = $except ?? ['per_page'];
@endphp

<form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
    @foreach(request()->except($except) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <select name="per_page" onchange="this.form.submit()" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 text-sm py-2 pl-3 pr-8 cursor-pointer">
        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('per_page') == 50 ? 
        'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
    </select>
</form>
