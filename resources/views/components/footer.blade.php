<footer>
    <div class="app-container">
        <x-brand.logo class="w-auto h-8 text-indigo-600" />

        <p class="pt-4 mt-4 text-sm text-gray-500 border-t border-gray-100">
            {{config('app.name')}} &copy; {{ now()->year }}
        </p>
    </div>
</footer>
