@extends('templates.redirection', ['title' => 'Multiple Options'])

@section('icon')
    <i class="fas fa-list-ul"></i>
@endsection

@section('heading', 'Multiple Options Available')

@section('message')
    The resource you requested is available in multiple formats or locations. Please select one to proceed.
@endsection

@section('action_area')
    <div class="text-left">
        <p class="small text-muted text-uppercase font-weight-bold mb-2 pl-1">Available Choices:</p>
        <ul class="choice-list">
            @foreach($choices as $choice)
                <li class="choice-item">
                    <a href="{{ $choice['url'] }}" class="btn-choice shadow-sm" target="{{ $choice['target'] ?? '_self' }}">
                        <span>
                            <i class="{{ $choice['icon'] ?? 'fas fa-link' }} mr-2 text-primary"></i> 
                            {{ $choice['label'] }}
                        </span>
                        <i class="fas fa-chevron-right text-muted small"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    
    <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Cancel
        </a>
    </div>
@endsection


@php
# Cara Pakai
// case 'download_report': 
    
//     // Siapkan opsi pilihan
//     $choices = [
//         [
//             'label' => 'Download as PDF',
//             'url'   => 'index.php?request=report_pdf',
//             'icon'  => 'fas fa-file-pdf'
//         ],
//         [
//             'label' => 'Export to Excel',
//             'url'   => 'index.php?request=report_excel',
//             'icon'  => 'fas fa-file-excel'
//         ],
//         [
//             'label' => 'View Web Version',
//             'url'   => 'index.php?page=report_view',
//             'icon'  => 'fas fa-globe',
//             'target'=> '_blank'
//         ]
//     ];

//     http_response_code(300); // Set Header 300
    
//     renderView('errors.300', [
//         'choices' => $choices
//     ]);
//     exit;
// break;
@endphp