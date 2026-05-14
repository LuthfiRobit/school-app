<title>@yield('title') | Able Pro Dashboard Template</title>
<!-- [Meta] -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description"
  content="Able Pro is a trending dashboard template built with the Bootstrap 5 design framework. It is available in multiple technologies, including Bootstrap, React, Vue, CodeIgniter, Angular, .NET, and more.">
<meta name="keywords"
  content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
<meta name="author" content="Phoenixcoded">

<!-- [Favicon] icon -->
<link rel="icon" href="{{ asset('template/admin/dist/assets/images/favicon.svg') }}" type="image/x-icon">

<!-- [Font] Family -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/fonts/inter/inter.css') }}" id="main-font-link" />
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/fonts/tabler-icons.min.css') }}">
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/fonts/feather.css') }}">
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/fonts/fontawesome.css') }}">
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/fonts/material.css') }}">
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/css/style.css') }}" id="main-style-link">
<link rel="stylesheet" href="{{ asset('template/admin/dist/assets/css/style-preset.css') }}">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
  /* === Select2: Sesuaikan ukuran dengan Able Pro form-control === */

  .select2-container {
    width: 100% !important;
  }

  /* Fix: Select2 di dalam Bootstrap input-group */
  .input-group>.select2-container--bootstrap-5 {
    flex: 1 1 auto;
    width: 1% !important;
  }

  .select2-container--bootstrap-5 .select2-selection {
    min-height: calc(1.5em + 0.75rem + 2px);
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: var(--bs-border-radius, 0.375rem);
    background-color: var(--bs-body-bg, #fff);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  /* Single Selection */
  .select2-container--bootstrap-5 .select2-selection--single {
    display: flex;
    align-items: center;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    padding: 0 !important;
    margin: 0 !important;
    color: var(--bs-body-color, #212529);
    line-height: 1.5;
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
    color: #6c757d;
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
    display: none !important;
  }

  /* =====================================================
     MULTIPLE SELECTION - DEFINITIF FIX
     Target both --bootstrap-5 AND --default themes
  ===================================================== */

  /* Container utama */
  .select2-container--bootstrap-5 .select2-selection--multiple,
  .select2-container--default .select2-selection--multiple {
    min-height: 42px !important;
    height: auto !important;
    padding: 4px 6px !important;
    cursor: text !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
  }

  /* List rendered - PAKSA flex wrap */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered,
  .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
    padding: 2px 0 !important;
    margin: 0 !important;
    list-style: none !important;
    width: 100% !important;
    box-sizing: border-box !important;
  }

  /* Setiap tag choice - FULL FLEX, tidak ada float */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice,
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: stretch !important;
    flex-shrink: 0 !important;
    float: none !important;
    background-color: #eeeeee !important;
    border: 1px solid #cccccc !important;
    border-radius: 4px !important;
    height: 26px !important;
    padding: 0 !important;
    margin: 0 !important;
    max-width: 200px !important;
    overflow: visible !important;
    position: static !important;
  }

  /* Tombol remove (x) - flex item, di sisi kiri */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove,
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    order: 0 !important;
    float: none !important;
    position: static !important;
    background: transparent !important;
    border: none !important;
    border-right: 1px solid #cccccc !important;
    border-radius: 3px 0 0 3px !important;
    color: #888 !important;
    cursor: pointer !important;
    width: 24px !important;
    min-width: 24px !important;
    height: 24px !important;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 14px !important;
    font-weight: bold !important;
    line-height: 1 !important;
  }

  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover,
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: #e74c3c !important;
    border-right-color: #c0392b !important;
    color: #fff !important;
  }

  /* Teks dalam tag - flex item, di sisi kanan */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__display,
  .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
    display: flex !important;
    align-items: center !important;
    flex: 1 !important;
    padding: 0 8px !important;
    font-size: 13px !important;
    color: #333 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1 !important;
  }

  /* Search field mengisi sisa ruang */
  .select2-container--bootstrap-5 .select2-selection--multiple .select2-search--inline,
  .select2-container--default .select2-selection--multiple .select2-search--inline {
    display: inline-flex !important;
    align-items: center !important;
    float: none !important;
    flex: 1 !important;
    min-width: 60px !important;
  }

  .select2-container--bootstrap-5 .select2-selection--multiple .select2-search--inline .select2-search__field,
  .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
    margin: 0 !important;
    padding: 0 4px !important;
    height: 26px !important;
    font-family: inherit !important;
    font-size: 0.875rem !important;
    background: transparent !important;
    border: none !important;
    outline: 0 !important;
    box-shadow: none !important;
    width: 100% !important;
  }

  /* Focus & Open */
  .select2-container--bootstrap-5.select2-container--focus .select2-selection,
  .select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }

  /* Dropdown */
  .select2-container--bootstrap-5 .select2-dropdown {
    border-color: #86b7fe;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    font-size: 0.9375rem;
    z-index: 1060; /* Higher than modal if needed */
  }

  .select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #0d6efd;
    color: #fff;
  }

  .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
  }

  .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: 0;
  }

  /* =====================================================
     DEFAULT THEME - Styling untuk Multiple Select
     (Digunakan pada Pilih Role di modal User)
  ===================================================== */

  /* Border & shape agar cocok dengan Bootstrap 5 */
  .select2-container--default .select2-selection--multiple {
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    background-color: #fff !important;
  }

  /* Focus state */
  .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    outline: 0 !important;
  }

  /* Dropdown */
  .select2-container--default .select2-dropdown {
    border: 1px solid #86b7fe !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    z-index: 9999 !important;
  }

  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd !important;
    color: #fff !important;
  }

  .select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #e9ecef !important;
    color: #495057 !important;
  }

  .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    padding: 0.4rem 0.75rem !important;
    font-size: 0.9rem !important;
  }

  .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    outline: 0 !important;
  }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@stack('css')