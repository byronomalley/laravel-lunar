<footer>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <x-brand.logo class="w-auto h-8 text-indigo-600" />

        <p class="pt-4 mt-4 text-sm text-gray-500 border-t border-gray-100">
            {{config('app.name')}} &copy; {{ now()->year }}
        </p>
    </div>
</footer>
