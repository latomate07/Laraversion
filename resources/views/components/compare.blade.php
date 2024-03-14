<div class="flex justify-center items-center min-h-screen">
    <div class="bg-gray-800">
        <!-- Sidebar Overlay -->
        <div x-show="openCompareView" class="fixed inset-0 z-50 overflow-hidden">
            <div x-show="openCompareView" x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-800 bg-opacity-75 transition-opacity"></div>
            <!-- Sidebar Content -->
            <section class="absolute inset-y-0 right-0 pl-10 max-w-full flex">
                <div x-show="openCompareView" x-transition:enter="transition-transform ease-out duration-300"
                    x-transition:enter-start="transform translate-x-full"
                    x-transition:enter-end="transform translate-x-0"
                    x-transition:leave="transition-transform ease-in duration-300"
                    x-transition:leave-start="transform translate-x-0"
                    x-transition:leave-end="transform translate-x-full" class="w-screen max-w-4xl flex flex-col gap-2">
                    <div class="h-full flex flex-col pt-6 bg-gray-900 shadow-xl">
                        <!-- Sidebar Header -->
                        <div class="flex items-center justify-between px-4">
                            <h2 class="text-xl font-semibold text-gray-100">
                                Compare versions
                            </h2>
                            <button x-on:click="openCompareView = false" class="text-gray-500 hover:text-gray-700">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" x-description="Heroicon name: x" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Search Input -->
                        <div class="mt-4 px-4 flex flex-col">
                            <label for="searchInCompareVersionView">
                                <span class="text-gray-400">Filter attributes</span>
                            </label>
                            <input x-model="searchInCompareVersionView" id="searchInCompareVersionView" type="text" placeholder="Try: event_type, commit_id, data.*, versionable.*, ..."
                                class="px-4 py-2 rounded-md bg-gray-800 text-gray-200 focus:outline-none focus:ring focus:border-blue-500 w-full">
                            <p class="px-1 text-xs text-gray-400">
                                Chain functions using dots. Check the <a class="text-blue-800 underline" target="_blank"
                                    href="https://github.com/alenaksu/json-viewer?tab=readme-ov-file#usage">documentation</a>
                            </p>
                        </div>
                        <div class="flex flex-row flex-nowrap w-full h-full gap-4">
                            <!-- Sidebar Content -->
                            <div class="mt-4 w-full px-4 h-full overflow-scroll">
                                <template x-if="canCompareVersion">
                                    <json-viewer
                                        class="json-viewer overflow-scroll shadow w-full min-h-full p-4 rounded-t-2xl bg-gray-800"
                                        x-bind:data="selectedVersions[0]" x-bind:filter="searchInCompareVersionView">
                                    </json-viewer>
                                </template>
                            </div>
                            <!-- Sidebar Content -->
                            <div class="mt-4 w-full px-4 h-full overflow-scroll">
                                <template x-if="canCompareVersion">
                                    <json-viewer
                                        class="json-viewer overflow-scroll shadow w-full min-h-full p-4 rounded-t-2xl bg-gray-800"
                                        x-bind:data="selectedVersions[1]" x-bind:filter="searchInCompareVersionView">
                                    </json-viewer>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>