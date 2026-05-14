<script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
<script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>

<script src="{{ asset('deskapp/src/plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
        $('.data-table').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { previous: "Sebelumnya", next: "Berikutnya" },
                zeroRecords: "Data tidak ditemukan"
            }
        });

        setTimeout(function () {
            $('.alert-dismissible').fadeOut('slow');
        }, 3500);
    });
</script>

@stack('scripts')
