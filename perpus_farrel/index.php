<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peminjaman Buku</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<header>
    <h1>Peminjaman Buku</h1>
</header>

<main>
    <section class="buku fade-card fade-delay-1">
    <h2>Daftar Buku</h2>
    <button onclick="showTambahBuku()">+ Tambah Buku</button>
    <button onclick="window.location.href='peminjaman.php'" class="fade-card fade-delay-2">
        Peminjaman Buku
    </button>

    <div id="listBuku" class="grid fade-card fade-delay-3"></div>
</section>



    <section class="peminjaman">
        <h2>Transaksi Peminjaman</h2>
        <div id="listPeminjaman" class="grid"></div>
    </section>
</main>

<!-- Modal Tambah Buku -->
<div id="modalBuku" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Tambah Buku</h3>
        <input type="text" id="judul" placeholder="Judul Buku">
        <input type="text" id="penulis" placeholder="Penulis">
        <input type="number" id="tahun" placeholder="Tahun Terbit">
        <input type="number" id="stok" placeholder="Stok">
        <button onclick="tambahBuku()">Simpan</button>
    </div>
</div>

<!-- Modal Edit Buku -->
<div id="modalEditBuku" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Buku</h3>
        <input type="hidden" id="editId">
        <input type="text" id="editJudul" placeholder="Judul Buku">
        <input type="text" id="editPenulis" placeholder="Penulis">
        <input type="number" id="editTahun" placeholder="Tahun Terbit">
        <input type="number" id="editStok" placeholder="Stok">
        <button onclick="editBuku()">Simpan</button>
    </div>
</div>

<script>
// Tunggu sampai DOM siap
document.addEventListener('DOMContentLoaded', function() {

    // Load daftar buku dari API
    function loadBuku(){
        axios.get('api.php?action=get_buku')
        .then(res=>{
            const list = document.getElementById('listBuku');
            list.innerHTML = '';
            res.data.forEach(buku=>{
                list.innerHTML += `<div class="card">
                    <h4>${buku.judul}</h4>
                    <p>Penulis: ${buku.penulis}</p>
                    <p>Tahun: ${buku.tahun}</p>
                    <p>Stok: ${buku.stok}</p>
                    <button onclick="showEditBuku(${buku.id},'${buku.judul}','${buku.penulis}',${buku.tahun},${buku.stok})">Edit</button>
                    <button onclick="hapusBuku(${buku.id})">Hapus</button>
                </div>`;
            });
        }).catch(err=>{
            console.error("Gagal load buku:", err);
        });
    }

    // Load daftar peminjaman
    function loadPeminjaman(){
        axios.get('api.php?action=get_peminjaman')
        .then(res=>{
            const list = document.getElementById('listPeminjaman');
            list.innerHTML = '';
            res.data.forEach(pinjam=>{
                list.innerHTML += `<div class="card">
                    <h4>${pinjam.judul}</h4>
                    <p>Peminjam: ${pinjam.nama_peminjam}</p>
                    <p>Tanggal Pinjam: ${pinjam.tanggal_pinjam}</p>
                    <p>Status: ${pinjam.status}</p>
                    ${pinjam.status==='Dipinjam'? `<button onclick="kembalikanBuku(${pinjam.id})">Kembalikan</button>`:``}
                </div>`;
            });
        }).catch(err=>{
            console.error("Gagal load peminjaman:", err);
        });
    }

    // Modal tambah buku
    window.showTambahBuku = function(){
        document.getElementById('modalBuku').style.display = 'block';
    }
    window.closeModal = function(){
        document.getElementById('modalBuku').style.display = 'none';
    }

    // Tambah buku
    window.tambahBuku = function(){
        const formData = new FormData();
        formData.append('judul', document.getElementById('judul').value);
        formData.append('penulis', document.getElementById('penulis').value);
        formData.append('tahun', document.getElementById('tahun').value);
        formData.append('stok', document.getElementById('stok').value);

        axios.post('api.php?action=tambah_buku', formData)
        .then(res=>{
            if(res.data.status==1){
                loadBuku();
                closeModal();
            } else {
                alert('Gagal tambah buku');
                console.log(res.data);
            }
        });
    }

    // Modal edit buku
    window.showEditBuku = function(id, judul, penulis, tahun, stok){
        document.getElementById('editId').value = id;
        document.getElementById('editJudul').value = judul;
        document.getElementById('editPenulis').value = penulis;
        document.getElementById('editTahun').value = tahun;
        document.getElementById('editStok').value = stok;
        document.getElementById('modalEditBuku').style.display = 'block';
    }
    window.closeEditModal = function(){
        document.getElementById('modalEditBuku').style.display = 'none';
    }

    // Edit buku
    window.editBuku = function(){
        const formData = new FormData();
        formData.append('id', document.getElementById('editId').value);
        formData.append('judul', document.getElementById('editJudul').value);
        formData.append('penulis', document.getElementById('editPenulis').value);
        formData.append('tahun', document.getElementById('editTahun').value);
        formData.append('stok', document.getElementById('editStok').value);

        axios.post('api.php?action=edit_buku', formData)
        .then(res=>{
            if(res.data.status==1){
                loadBuku();
                closeEditModal();
            } else {
                alert('Gagal edit buku');
                console.log(res.data);
            }
        });
    }

    // Hapus buku
    window.hapusBuku = function(id){
        if(!confirm("Apakah yakin ingin menghapus buku ini?")) return;

        const formData = new FormData();
        formData.append('id', id);

        axios.post('api.php?action=hapus_buku', formData)
        .then(res=>{
            if(res.data.status==1){
                loadBuku();
            } else {
                alert('Gagal hapus buku');
                console.log(res.data);
            }
        });
    }

    // Kembalikan buku
    window.kembalikanBuku = function(id){
        const formData = new FormData();
        formData.append('id', id);

        axios.post('api.php?action=kembalikan_buku', formData)
        .then(res=>{
            if(res.data.status==1){
                loadPeminjaman();
            } else {
                alert('Gagal mengembalikan buku');
                console.log(res.data);
            }
        });
    }

    // Load data awal
    loadBuku();
    loadPeminjaman();
});
</script>
</body>
</html>
