<div x-show="openRevertView" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
        <div x-cloak @click="openRevertView = false" x-show="openRevertView"
            x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-600 bg-opacity-40" aria-hidden="true"></div>

        <div x-cloak x-show="openRevertView" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-gray-800 rounded-lg shadow-xl 2xl:max-w-2xl">
            <div class="flex items-center justify-between space-x-4">
                <h1 class="text-xl font-medium text-gray-400 ">
                    Model Version Restoration
                </h1>
                <button @click="openRevertView = false" class="text-gray-600 focus:outline-none hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
            <p class="my-2 text-sm text-gray-500 ">
                Revert the selected model to this version
            </p>
            <template x-if="currentVersionInView">
                <json-viewer id="json-viewer"
                    class="shadow-xl shadow-gray-700 w-full min-h-full p-4 rounded-2xl bg-gray-800 border border-gray-700 max-h-96 overflow-auto"
                    x-bind:data="currentVersionInView" x-bind:filter="searchInVersionView">
                </json-viewer>
            </template>
            <div class="flex justify-end mt-6">
                <button type="button" x-on:click="revertVersion"
                    class="px-3 py-2 text-sm tracking-wide text-gray-200 transition-colors duration-200 transform rounded-md bg-blue-900 hover:bg-blue-900 focus:outline-none focus:bg-blue-900 focus:ring focus:ring-blue-700 focus:ring-opacity-50">
                    Revert to this version
                </button>
            </div>
        </div>
    </div>
</div>