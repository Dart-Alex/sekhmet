@if ($paginator->hasPages())
    <nav class="pagination is-centered" role="navigation">
		{{-- Previous Page Link --}}
		@if(!$paginator->onFirstPage())
            <a class="page-selector-item pagination-previous" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
		@endif
		{{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="page-selector-item pagination-next" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
        @endif
		<ul class="pagination-list">
			{{-- Pagination Elements --}}
			@foreach ($elements as $element)
				{{-- "Three Dots" Separator --}}
				@if (is_string($element))
					<li>
						<span class="pagination-elipsis" aria-disabled="true">{{ $element }}</span>
					</li>
				@endif
				{{-- Array Of Links --}}
				@if (is_array($element))
					@foreach ($element as $page => $url)
						@if ($page == $paginator->currentPage())
							<li><a class="page-selector-item pagination-link page-current" aria-current="page">{{ $page }}</a></li>
						@else
							<li><a href="{{ $url }}" class="page-selector-item pagination-link">{{ $page }}</a></li>
						@endif
					@endforeach
				@endif
			@endforeach
		</ul>


		</nav>
@endif
