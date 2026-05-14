<div class="modal fade" id="detailTipeUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Tipe Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Proyek</th>
                                <td id="modalNamaProyek"></td>
                            </tr>
                            <tr>
                                <th>Kode Tipe</th>
                                <td id="modalKodeTipe"></td>
                            </tr>
                            <tr>
                                <th>Nama Tipe</th>
                                <td id="modalNamaTipe"></td>
                            </tr>
                            <tr>
                                <th>Luas Tanah</th>
                                <td id="modalLuasTanah"></td>
                            </tr>
                            <tr>
                                <th>Luas Bangunan</th>
                                <td id="modalLuasBangunan"></td>
                            </tr>
                            <tr>
                                <th>Jumlah Kamar</th>
                                <td id="modalJumlahKamar"></td>
                            </tr>
                            <tr>
                                <th>Jumlah WC</th>
                                <td id="modalJumlahWc"></td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td id="modalHarga"></td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td id="modalStok"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <img src="" id="modalGambar" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function detailTipe(id) {
    $.ajax({
        url: '{{ route("admin.properti.tipe-unit.detail", "") }}/' + id,
        method: 'GET',
        success: function(data) {
            $('#modalNamaProyek').text(data.nama_proyek);
            $('#modalKodeTipe').text(data.kode_tipe);
            $('#modalNamaTipe').text(data.nama_tipe);
            $('#modalLuasTanah').text(data.luas_tanah + ' m²');
            $('#modalLuasBangunan').text(data.luas_bangunan + ' m²');
            $('#modalJumlahKamar').text(data.jumlah_kamar || '-');
            $('#modalJumlahWc').text(data.jumlah_wc || '-');
            $('#modalHarga').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.harga));
            $('#modalStok').text(data.stok_tersedia);
            
            if (data.gambar) {
                $('#modalGambar').attr('src', '/storage/' + data.gambar);
            } else {
                $('#modalGambar').attr('src', '{{ asset('deskapp/vendors/images/img2.jpg') }}');
            }
            
            $('#detailTipeUnitModal').modal('show');
        },
        error: function() {
            Swal.fire('Error!', 'Gagal mengambil data', 'error');
        }
    });
}
</script>