<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Versions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-900 text-gray-200 font-sans">
    <div class="container mx-auto px-4 py-8">
        <div class="relative mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-semibold">Model Versions</h1>
            <div class="space-x-2 flex items-center">
                <input type="text"
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring focus:border-blue-500"
                    placeholder="Search versions...">
                <select
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring focus:border-blue-500">
                    <option value="">All Models</option>
                    @foreach($models as $model)
                    <option value="{{ $model->versionable_type }}">{{ $model->versionable_type }}</option>
                    @endforeach
                </select>
                <button
                    class="px-4 py-2 rounded-md bg-blue-800 text-white hover:bg-blue-900 transition duration-200 ease-in-out">All</button>
                <button
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 hover:bg-gray-800 transition duration-200 ease-in-out">Created</button>
                <button
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 hover:bg-gray-800 transition duration-200 ease-in-out">Updated</button>
                <button
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 hover:bg-gray-800 transition duration-200 ease-in-out">Deleted</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($versions as $version)
            <div
                class="bg-gray-800 flex flex-col gap-2 rounded-lg shadow-md p-6 relative cursor-pointer hover:bg-blue-800 hover:shadow hover:scale-105 hover:rotate-3 transition-all ease-in-out duration-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">{{ str_replace("App\Models\\", "", $version->versionable_type) ."
                        #".$version->versionable_id }}</h2>
                    <div class="space-x-2 flex items-center">
                        <button
                            class="px-2 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                            </svg>
                        </button>
                        <button
                            class="px-2 py-1 rounded-md bg-green-600 text-white hover:bg-green-700 transition duration-200 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-gray-400">Version: {{ $version->commit_id }}</p>
                <div class="flex flex-col justify-between items-center mt-4">
                    <p class="text-sm text-gray-400">Event: {{ $version->event_type }}</p>
                    <p class="text-sm text-gray-400">{{ $version->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>

</html>