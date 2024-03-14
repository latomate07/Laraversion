<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Laraversion - Model Versions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.7/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@alenaksu/json-viewer@2.0.0/dist/json-viewer.bundle.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body class="bg-gray-900 text-gray-200 font-sans">
    <div class="container mx-auto px-4 py-8" x-data="laraversion">
        <div class="relative mb-8 flex justify-between items-center">
            <h1 class="text-3xl font-semibold">Model Versions</h1>
            <div class="space-x-2 flex items-center">
                <input type="text" x-model="search"
                    class="px-4 py-2 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring focus:border-blue-500"
                    placeholder="Global search...">
                <select x-model="selectedModel"
                    class="cursor-pointer px-4 py-2 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring focus:border-blue-500 w-40">
                    <option value="">All Models</option>
                    @foreach($models as $model)
                    <option value="{{ str_replace(" App\Models\\", "" , $model->versionable_type) }}">
                        {{ str_replace("App\Models\\", "", $model->versionable_type) }}
                    </option>
                    @endforeach
                </select>
                <select x-model="selectedEvent"
                    class="cursor-pointer px-4 py-2 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring focus:border-blue-500 w-40">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                    <option value="{{ $event->event_type }}">{{ $event->event_type }}</option>
                    @endforeach
                </select>
                <button x-on:click="showCompareView" title="Compare 2 versions of model" :disabled="!canCompareVersion"
                    :class="{'cursor-not-allowed disabled': !canCompareVersion}"
                    class="px-4 py-2 rounded-md bg-blue-800 text-white hover:bg-blue-900 transition duration-200 ease-in-out">
                    Compare
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($versions as $version)
            <div x-on:click="handleSelectedItem($event)"
                class="version-card bg-gray-800 flex flex-col gap-2 rounded-lg shadow-md p-6 relative cursor-pointer hover:bg-blue-800 hover:shadow hover:scale-105 hover:rotate-3 transition-all ease-in-out duration-200"
                data-model="{{ str_replace(" App\Models\\", "" , $version->versionable_type) }}"
                data-event="{{ $version->event_type }}" data-version="{{ json_encode($version) }}">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">
                        {{ str_replace("App\Models\\", "", $version->versionable_type) ." #".$version->versionable_id }}
                    </h2>
                    <div class="space-x-2 flex items-center">
                        <button
                            class="px-2 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition duration-200 ease-in-out">
                            <svg x-on:click="showRevertView" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                            </svg>
                        </button>
                        <button
                            class="px-2 py-1 rounded-md bg-green-600 text-white hover:bg-green-700 transition duration-200 ease-in-out">
                            <svg x-on:click="showVersionView($event)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                viewBox="0 0 20 20" fill="currentColor">
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

        <template x-if="openVersionView">
            @include('laraversion::components.view')
        </template>

        <template x-if="openCompareView">
            @include('laraversion::components.compare')
        </template>

        <template x-if="openRevertView">
            @include('laraversion::components.revert')
        </template>

        <div class="mt-4 flex justify-center">
            {{ $versions->links('laraversion::components.pagination') }}
        </div>
    </div>
</body>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('laraversion', () => ({
            search: '',
            selectedModel: '',
            selectedEvent: '',
            selectedVersions: [],
            selectedVersionsAsJSON: [],
            canCompareVersion: false,
            openVersionView: false,
            openCompareView: false,
            openRevertView: false,
            currentVersionInView: {},
            searchInVersionView: '',
            searchInCompareVersionView: '',
            init() {
                this.$watch('selectedModel', (value) => {
                    this.applyFilters();
                });
                this.$watch('selectedEvent', (value) => {
                    this.applyFilters();
                });
                this.$watch('search', (value) => {
                    this.applyFilters();
                });
                this.$watch('selectedVersions', (value) => {
                    switch(value.length) {
                        case 2:
                            this.canCompareVersion = true;
                            break;
                        default:
                            this.canCompareVersion = false;
                            break;
                    }
                });
                this.$watch('openVersionView', (value) => {
                    if (!value) {
                        document.body.style.overflow = 'auto';
                        return;
                    }
                    document.body.style.overflow = 'hidden';
                });
                this.$watch('openCompareView', (value) => {
                    if (!value) {
                        document.body.style.overflow = 'auto';
                        return;
                    }
                    document.body.style.overflow = 'hidden';
                });
                this.$watch('openRevertView', (value) => {
                    if (!value) {
                        document.body.style.overflow = 'auto';
                        return;
                    }
                    document.body.style.overflow = 'hidden';
                });
                this.$watch('searchInVersionView', (value) => {
                    const viewer = document.querySelector('#json-viewer');
                    if(!value) {
                        viewer.resetFilter();
                        return;
                    };

                    // Filter
                    viewer.filter(value);
                });
                this.$watch('searchInCompareVersionView', (value) => {
                    const viewers = document.querySelectorAll('.json-viewer');
                    Array.from(viewers).forEach((viewer) => {
                        if(!value) {
                            viewer.resetFilter();
                            return;
                        };

                        // Filter
                        viewer.filter(value);
                    });
                });
            },
            applyFilters() {
                setTimeout(() => {
                    const versionCards = document.querySelectorAll('.version-card');
                    versionCards.forEach((card) => {
                        const model = card.getAttribute('data-model');
                        const event = card.getAttribute('data-event');
                        const versionData = JSON.parse(card.getAttribute('data-version'));

                        // Check if search term matches any part of the version data
                        const searchTerm = this.search.toLowerCase();
                        const searchData = Object.values(versionData).join(' ').toLowerCase();
                        const searchTermFound = searchData.includes(searchTerm);

                        if ((this.selectedModel && model !== this.selectedModel) || (this.selectedEvent && event !== this.selectedEvent) || !searchTermFound) {
                            card.classList.add('hidden');
                        } else {
                            card.classList.remove('hidden');
                        }
                    });
                }, 10);
            },
            handleSelectedItem(event) {
                if(event.target.tagName == 'svg' || event.target.tagName == 'path') return;

                const targetElement = event.currentTarget;
                const version = JSON.parse(targetElement.getAttribute('data-version'));
                const versionAsText = targetElement.getAttribute('data-version');

                // Check if the version is already selected
                const index = this.selectedVersionsAsJSON.findIndex(v => v.id === version.id);

                // If the version is already selected, remove it from the list
                if (index !== -1) {
                    this.selectedVersions.splice(index, 1);
                    this.selectedVersionsAsJSON.splice(index, 1);
                } else {
                    // If the version is not already selected and there are less than 2 selected versions, add it to the list
                    if (this.selectedVersions.length < 2) {
                        this.selectedVersions.push(versionAsText);
                        this.selectedVersionsAsJSON.push(version);
                    }
                }

                // Update card bg color based on the selection state
                targetElement.classList.toggle('bg-blue-800');

                // Update the opacity of all version cards based on the number of selected versions
                const versionCards = document.querySelectorAll('.version-card');
                versionCards.forEach(card => {
                    const cardVersion = JSON.parse(card.getAttribute('data-version'));
                    const isCardSelected = this.selectedVersionsAsJSON.some(v => v.id === cardVersion.id);
                    const isMaxSelectionReached = this.selectedVersions.length >= 2;

                    // Apply opacity based on the selection state
                    card.classList.toggle('opacity-50', isMaxSelectionReached && !isCardSelected);
                    card.classList.toggle('pointer-events-none', isMaxSelectionReached && !isCardSelected);
                });
            },
            showVersionView(event) {
                const targetElement = event.target.closest('.version-card');
                this.openVersionView = !this.openVersionView;
                const version = targetElement.getAttribute('data-version');
                this.currentVersionInView = version;
            },
            showCompareView(event) {
                // Check if two selected versions have the same versionable_type
                const versionableTypes = this.selectedVersionsAsJSON.map(v => v.versionable_type);
                const uniqueVersionableTypes = [...new Set(versionableTypes)];

                if (uniqueVersionableTypes.length !== 1) {
                    // Alert the user if the selected versions belong to different models
                    alert("You can only compare versions of the same model.");
                } else {
                    // If versions belong to the same model, show the comparison view
                    this.searchInCompareVersionView = 'data.*';
                    this.openCompareView = !this.openCompareView;
                }
            },
            showRevertView(event) {
                this.openRevertView = !this.openRevertView;
                const targetElement = event.target.closest('.version-card');
                const version = targetElement.getAttribute('data-version');
                this.currentVersionInView = version;
            },
            async revertVersion() {
                if(!confirm('Are you sure you want to revert the model to this version?')) return;

                // Send a POST request to the server to revert the model to the selected version
                try {
                    const csrfToken = "{{ csrf_token() }}";
                    const response = await fetch("{{ route('laraversion.revert') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        },
                        body: JSON.stringify({
                            model_id: JSON.parse(this.currentVersionInView).versionable_id,
                            version_id: JSON.parse(this.currentVersionInView).commit_id,
                        }),
                    });

                    const data = await response.json();

                    // Display a success message and refresh the page
                    if (response.ok) {
                        alert(data.message);
                        this.openRevertView = false;
                        location.reload();
                    } else {
                        // Handle the error
                        console.error(data);
                        alert('An error occurred while reverting the model.');
                    }
                } catch (error) {
                    // Handle the network error
                    console.error(error);
                    alert('An error occurred while sending the request.');
                }
            },
        }));
    });
</script>

<style>
    .json-viewer {
        /* Background, font and indentation */
        --background-color: #2a2f3a;
        --color: #f8f8f2;
        --font-family: monaco, Consolas, 'Lucida Console', monospace;
        --font-size: 1rem;
        --indent-size: 1.5em;
        --indentguide-size: 1px;
        --indentguide-style: solid;
        --indentguide-color: #333;
        --indentguide-color-active: #666;
        --indentguide: var(--indentguide-size) var(--indentguide-style) var(--indentguide-color);
        --indentguide-active: var(--indentguide-size) var(--indentguide-style) var(--indentguide-color-active);

        /* Types colors */
        --string-color: #a3eea0;
        --number-color: #d19a66;
        --boolean-color: #4ba7ef;
        --null-color: #df9cf3;
        --property-color: #6fb3d2;

        /* Collapsed node preview */
        --preview-color: rgba(222, 175, 143, 0.9);

        /* Search highlight color */
        --highlight-color: #6fb3d2;
    }
</style>

</html>