@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        // Calculate sliding window of 5 pages
        $start = max(1, $current - 2);
        $end = min($last, $start + 4);

        // Adjust start if we're near the end
        if ($end - $start < 4) {
            $start = max(1, $end - 4);
        }
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">
        <ul class="pagination-custom">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link-custom">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link-custom" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Sliding Window of 5 Pages --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link-custom">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link-custom" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link-custom" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link-custom">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination-custom {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 0.4rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-item {
            display: inline-block;
        }

        .page-link-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 0.75rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #555;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .page-link-custom:hover {
            background-color: var(--bs-primary-light, #f9ebeb);
            color: var(--bs-primary-dark, #b0657b);
            border-color: var(--bs-primary-light, #f9ebeb);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(216, 164, 164, 0.2);
        }

        .page-item.active .page-link-custom {
            background: linear-gradient(135deg, var(--bs-primary, #d8a4a4) 0%, var(--bs-primary-dark, #b0657b) 100%);
            color: white;
            border-color: var(--bs-primary, #d8a4a4);
            box-shadow: 0 4px 15px rgba(216, 164, 164, 0.4);
            font-weight: 600;
        }

        .page-item.disabled .page-link-custom {
            color: #ccc;
            background-color: #f8f8f8;
            border-color: #f0f0f0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Mobile Optimizations */
        @media (max-width: 576px) {
            .pagination-custom {
                gap: 0.3rem;
            }

            .page-link-custom {
                min-width: 36px;
                height: 36px;
                padding: 0.4rem 0.6rem;
                font-size: 0.85rem;
                border-radius: 6px;
            }
        }

        /* Tablet */
        @media (min-width: 577px) and (max-width: 768px) {
            .page-link-custom {
                min-width: 38px;
                height: 38px;
                font-size: 0.9rem;
            }
        }
    </style>
@endif