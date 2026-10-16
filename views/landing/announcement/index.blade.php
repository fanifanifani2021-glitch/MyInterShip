@extends('templates.home')

@section('head-scripts')
<style>
    /* --- Modern Announcement Header --- */
    .page-header {
        padding-top: 130px;
        padding-bottom: 50px;
        background: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        text-align: center;
    }

    .page-title {
        color: var(--primary-color, #005b9f);
        font-weight: 800;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
    }

    /* Search Bar Design */
    .search-container {
        position: relative;
        max-width: 580px;
        margin: 20px auto 0;
        display: flex;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        overflow: hidden;
    }

    .search-input {
        height: 52px;
        border: 1px solid #e2e8f0;
        border-right: none;
        padding-left: 20px;
        padding-right: 20px;
        font-size: 0.95rem;
        border-radius: 8px 0 0 8px !important;
    }

    .search-input:focus {
        box-shadow: none;
        border-color: #cbd5e1;
    }

    .search-btn {
        height: 52px;
        width: 60px;
        background: var(--accent-color, #005b9f);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.2s ease;
        border-radius: 0 8px 8px 0 !important;
    }

    .search-btn:hover {
        background: var(--primary-color, #00417e);
    }

    /* --- Modern Announcement Card Grid --- */
    .announcement-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #eef2f6;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .announcement-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .card-meta {
        font-size: 0.82rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .card-category {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-item-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-item-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    .card-item-title a:hover {
        color: var(--accent-color, #005b9f);
    }

    .card-excerpt {
        color: #475569;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 18px;
    }

    .card-footer-info {
        border-top: 1px solid #f1f5f9;
        padding-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn-read-more {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--accent-color, #005b9f);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: gap 0.2s;
    }

    .btn-read-more:hover {
        gap: 8px;
        color: var(--primary-color, #00417e);
    }

    /* Loader Spinner */
    .custom-loader {
        width: 42px;
        height: 42px;
        border: 4px solid #f1f5f9;
        border-top-color: var(--accent-color, #005b9f);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 40px auto 15px;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* Custom Pagination */
    .pagination .page-item .page-link {
        border-radius: 8px !important;
        margin: 0 3px;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        padding: 8px 16px;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--accent-color, #005b9f);
        border-color: var(--accent-color, #005b9f);
        color: white;
    }
</style>
@endsection

@section('content')
<main id="main">

    {{-- Page Header --}}
    <section class="page-header">
        <div class="container">
            <h2 class="page-title" data-aos="fade-up">Announcements & News</h2>
            <p class="text-muted" data-aos="fade-up" data-aos-delay="100">Stay informed with official updates, news, and internship information.</p>

            <div class="search-container" data-aos="fade-up" data-aos-delay="200">
                <input type="text" id="search_box" class="form-control search-input" placeholder="Search announcement title..." autocomplete="off">
                <button class="search-btn" type="button" id="btn_search">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- Announcement Grid Section --}}
    <section class="announcements-section py-5" style="background-color: #f8fafc;">
        <div class="container">
            
            {{-- Header Counter --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold text-secondary mb-0" id="total_counter_text">Loading announcements...</h6>
            </div>

            {{-- Container Dynamic Content --}}
            <div id="dynamic_content">
                <div class="text-center py-5">
                    <div class="custom-loader"></div>
                    <p class="text-muted small">Fetching latest announcements...</p>
                </div>
            </div>

            {{-- Container Pagination --}}
            <div class="d-flex justify-content-center mt-5" id="pagination_container"></div>

        </div>
    </section>

    @include('partials.contact-us')

</main>
@endsection

@section('footer-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let _token = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function() {
        // First Load
        load_data(1);

        // AJAX Loader Function
        function load_data(page = 1, query = '') {
            $('#dynamic_content').html(`
                <div class="text-center py-5">
                    <div class="custom-loader"></div>
                    <p class="text-muted small">Loading announcements...</p>
                </div>
            `);

            $.ajax({
                url: "{{ router('ajax.public.announcement.list') }}",
                method: "POST",
                dataType: "json",
                data: {
                    _token: _token,
                    page: page,
                    query: query
                },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#total_counter_text').text(`Showing ${res.data.length} of ${res.total_data} announcements`);
                        renderCards(res.data);
                        renderPagination(res.current_page, res.total_pages, query);
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    $('#dynamic_content').html(`
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle text-danger display-5"></i>
                            <h6 class="mt-3 text-muted">Failed to load announcement data.</h6>
                            <button onclick="location.reload()" class="btn btn-sm btn-outline-primary mt-2">Retry</button>
                        </div>
                    `);
                }
            });
        }

        // Render Card HTML
        function renderCards(items) {
            if (!items || items.length === 0) {
                $('#dynamic_content').html(`
                    <div class="text-center py-5 bg-white rounded-3 border">
                        <i class="bi bi-inbox text-muted display-4"></i>
                        <h5 class="mt-3 text-secondary fw-bold">No Announcements Found</h5>
                        <p class="text-muted small">Try searching with a different keyword or check back later.</p>
                    </div>
                `);
                return;
            }

            let html = '<div class="row g-4">';
            items.forEach(item => {
                html += `
                <div class="col-md-6 col-lg-4">
                    <div class="announcement-card">
                        <div>
                            <div class="card-meta">
                                <span class="card-category">${item.category}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>${item.created_at}</span>
                            </div>
                            <h3 class="card-item-title">
                                <a href="${item.detail_url}">${item.title}</a>
                            </h3>
                            <p class="card-excerpt">${item.excerpt}</p>
                        </div>
                        <div class="card-footer-info">
                            <small class="text-muted"><i class="bi bi-person me-1"></i>${item.created_by}</small>
                            <a href="${item.detail_url}" class="btn-read-more">
                                Read More <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';

            $('#dynamic_content').html(html);
        }

        // Render Pagination HTML
        function renderPagination(currentPage, totalPages, query) {
            if (totalPages <= 1) {
                $('#pagination_container').html('');
                return;
            }

            let paginationHtml = '<nav><ul class="pagination">';
            
            // Previous
            if (currentPage > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></a></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                let activeClass = i === currentPage ? 'active' : '';
                paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }

            // Next
            if (currentPage < totalPages) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></a></li>`;
            }

            paginationHtml += '</ul></nav>';
            $('#pagination_container').html(paginationHtml);
        }

        // Event Pagination Click
        $(document).on('click', '.pagination .page-link', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            if (page) {
                let query = $('#search_box').val();
                $('html, body').animate({ scrollTop: $(".page-header").offset().top - 50 }, 400);
                load_data(page, query);
            }
        });

        // Event Search Live
        let searchTimer;
        $('#search_box').on('keyup', function() {
            clearTimeout(searchTimer);
            let query = $(this).val();
            searchTimer = setTimeout(function() {
                load_data(1, query);
            }, 400);
        });
    });
</script>
@endsection