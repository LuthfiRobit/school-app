<!-- Required Js -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script src="{{ asset('template/admin/dist/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/script.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/theme.js') }}"></script>
<script src="{{ asset('template/admin/dist/assets/js/plugins/feather.min.js') }}"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    $(document).ready(function() {
        // Init Select2
        $('.select2-native').each(function() {
            var $parent = $(this).closest('.input-group').length
                ? $(document.body)
                : $(this).parent();

            $(this).select2({
                theme: 'bootstrap-5',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
                dropdownParent: $parent,
            });
        });

        // Sinkronkan tinggi Select2 dengan form-control aktual template
        syncSelect2Height();
    });

    function syncSelect2Height() {
        // Ukur tinggi aktual form-control yang dirender oleh template
        var $ref = $('<input type="text" class="form-control">').appendTo('body');
        var actualHeight = $ref.outerHeight();
        $ref.remove();

        // Terapkan tinggi yang sama ke semua Select2 selection container
        $('.select2-container--bootstrap-5 .select2-selection').css({
            'height': actualHeight + 'px',
            'min-height': actualHeight + 'px',
        });
    }

    change_box_container('false');
    layout_caption_change('true');
    layout_rtl_change('false');
    preset_change("preset-1");
</script>

@stack('js')
