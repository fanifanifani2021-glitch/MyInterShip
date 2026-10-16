@extends('templates.home')

@section('head-scripts')
<style>
    /* --- Page Specific Styles --- */
    
    .article-header {
        background: #fff;
        padding: 100px 0 40px;
        text-align: center;
        border-bottom: 1px solid var(--border-color, #eee);
    }

    /* Breadcrumb */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 20px;
    }
    .breadcrumb-item a {
        color: var(--text-muted, #6c757d);
        text-decoration: none;
        font-weight: 600;
    }
    .breadcrumb-item.active {
        color: var(--primary-color, #005b9f);
        font-weight: 700;
    }

    /* Meta Info Badge */
    .article-meta {
        display: inline-flex;
        align-items: center;
        gap: 20px;
        color: var(--text-muted, #6c757d);
        font-size: 0.88rem;
        margin-top: 15px;
        padding: 8px 20px;
        border: 1px solid #eef2f6;
        background: var(--bg-light, #f8fafc);
        border-radius: 6px;
    }

    .article-meta i {
        color: var(--accent-color, #005b9f);
        margin-right: 6px;
    }

    /* Article Body */
    .article-body {
        background-color: #fff;
        padding: 40px 0 80px;
    }

    .article-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--text-dark, #1e293b);
        margin-top: 25px;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border: 1px solid #e2e8f0;
        padding: 5px;
        margin: 20px 0;
        border-radius: 6px;
    }

    .featured-image-container {
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        padding: 8px;
        background: #fff;
        border-radius: 8px;
    }
    
    .featured-image-container img {
        border-radius: 6px;
    }

    /* Attachment Box */
    .attachment-box {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--accent-color, #005b9f);
        padding: 20px 25px;
        border-radius: 8px;
        margin-top: 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .attachment-box:hover {
        background-color: var(--bg-light, #f8fafc);
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Buttons */
    .btn-download {
        background: var(--accent-color, #005b9f);
        color: white;
        border: 1px solid var(--accent-color, #005b9f);
        border-radius: 6px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-download:hover {
        background: var(--primary-color, #00417e);
        border-color: var(--primary-color, #00417e);
        color: white;
        transform: translateY(-2px);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        color: var(--text-dark, #334155);
        font-size: 14px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid #cbd5e1;
        padding: 10px 20px;
        background: #fff;
        border-radius: 6px;
    }

    .btn-back:hover {
        background: var(--bg-light, #f8fafc);
        border-color: #94a3b8;
        color: var(--primary-color, #005b9f);
    }
</style>
@endsection

@section('content')
<main id="main">

    <section class="article-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-3">
                    <li class="breadcrumb-item"><a href="{{ router('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ router('announcement.index') }}">Announcements</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>

            <h1 class="fw-bold mb-3" data-aos="fade-up" style="color: var(--primary-color, #005b9f);">
                {{ $announcement->title ?? 'Untitled Announcement' }}
            </h1>

            <div class="article-meta" data-aos="fade-up" data-aos-delay="100">
                <span>
                    <i class="bi bi-calendar-event"></i> 
                    {{ !empty($announcement->created_at) ? date('M d, Y', strtotime($announcement->created_at)) : 'Recently' }}
                </span>
                <span style="border-left: 1px solid #cbd5e1; padding-left: 15px;">
                    <i class="bi bi-person-circle"></i> 
                    {{ $announcement->created_by ?? 'Admin' }}
                </span>
                <span style="border-left: 1px solid #cbd5e1; padding-left: 15px;">
                    <i class="bi bi-tag-fill"></i> 
                    {{ $announcement->category ?? 'Information' }}
                </span>
                <span style="border-left: 1px solid #cbd5e1; padding-left: 15px;">
                    <i class="bi bi-eye-fill"></i> 
                    {{ (int)($announcement->visitor_count ?? 0) }} Views
                </span>
            </div>
        </div>
    </section>

    <section class="article-body">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9"> 
                    
                    {{-- Cover Gambar Pengumuman (Jika Ada) --}}
                    @if(!empty($announcement->picture) && $announcement->picture !== '-')
                        <div class="featured-image-container" data-aos="fade-up" data-aos-delay="200">
                            <img class="img-fluid w-100" src="index.php?viewer=announcement&id={{ base64_encode($announcement->id_announcement) }}" alt="{{ $announcement->title }}" onerror="this.parentElement.style.display='none'">
                        </div>
                    @endif

                    {{-- Isi Konten dari CKEditor --}}
                    <div class="article-content" data-aos="fade-up" data-aos-delay="300">
                        {!! html_entity_decode(htmlspecialchars_decode($announcement->content ?? '')) !!}
                    </div>

                    {{-- Download Resources / Attachment --}}
                    @if ((int)$announcement->have_attachment === 1)
                        <div class="attachment-box" data-aos="fade-up">
                            <div>
                                <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-paperclip me-2"></i> Attached Resources</h6>
                                <p class="text-muted small mb-0">Official documents available for download.</p>
                            </div>
                            <a href="index.php?viewer=attachment_announcement&id={{ base64_encode($announcement->id_announcement) }}" target="_blank" class="btn-download" download>
                                <i class="bi bi-download me-2"></i> Download Attachment
                            </a>
                        </div>
                    @endif

                    <hr class="my-5" style="border-color: #f1f5f9;">
                    
                    {{-- Footer Navigation & Share --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ router('announcement.index') }}" class="btn-back">
                            <i class="bi bi-arrow-left me-2"></i> Back to Announcements
                        </a>
                        
                        <div class="social-share">
                            <span class="text-muted small me-3 fw-bold">SHARE:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Url::base()) }}" target="_blank" class="text-muted me-3"><i class="bi bi-facebook fs-5"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(Url::base()) }}&text={{ urlencode($announcement->title) }}" target="_blank" class="text-muted me-3"><i class="bi bi-twitter fs-5"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Url::base()) }}" target="_blank" class="text-muted"><i class="bi bi-linkedin fs-5"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    @include('partials.contact-us')

</main>
@endsection