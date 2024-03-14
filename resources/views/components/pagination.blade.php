@if ($paginator->hasPages())
    <ul class="flex gap-4 list-none">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="block w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full cursor-not-allowed">
                    <span class="sr-only">@lang('pagination.previous')</span>
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M11.707 4.293a1 1 0 011.414 1.414l-4 4a1 1 0 010 1.414l4 4a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4z" clip-rule="evenodd" />
                    </svg>
                </span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="block w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full hover:bg-gray-400 focus:outline-none focus:ring focus:bg-gray-400 transition duration-200">
                    <span class="sr-only">@lang('pagination.previous')</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                      </svg>
                                         
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li aria-disabled="true">
                    <span class="block w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full cursor-not-allowed">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li aria-current="page">
                            <span class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center rounded-full">{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}" class="w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full hover:bg-gray-400 focus:outline-none focus:ring focus:bg-gray-400 transition duration-200">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full hover:bg-gray-400 focus:outline-none focus:ring focus:bg-gray-400 transition duration-200">
                    <span class="sr-only">@lang('pagination.next')</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>                      
                </a>
            </li>
        @else
            <li aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="w-8 h-8 bg-gray-800 text-gray-600 flex items-center justify-center rounded-full cursor-not-allowed">
                    <span class="sr-only">@lang('pagination.next')</span>
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.293 15.707a1 1 0 01-1.414-1.414l4-4a1 1 0 010 1.414l-4 4a1 1 0 011.414 0l4-4a1 1 0 010-1.414l-4-4a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4z" clip-rule="evenodd" />
                    </svg>
                </span>
            </li>
        @endif
    </ul>
@endif
