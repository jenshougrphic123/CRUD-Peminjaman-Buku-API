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
    
<header class="fade-card fade-delay-1">
    <h1>Transaksi Peminjaman Buku</h1>
</header>

<div class="back-btn fade-card fade-delay-1">
    <button onclick="window.location.href='index.php'">← Kembali ke Halaman Utama</button>
</div>


<main>
    <div class="card fade-card fade-delay-2 form-card">
        <h3>Pinjam Buku</h3>

        <div class="input-group fade-card fade-delay-3">
            <label for="bukuSelect">Pilih Buku</label>
            <select id="bukuSelect"></select>
        </div>

        <div class="input-group fade-card fade-delay-4">
            <label for="namaPeminjam">Nama Peminjam</label>
            <input type="text" id="namaPeminjam" placeholder="Masukkan nama...">
        </div>

        <button onclick="pinjamBuku()" class="fade-card fade-delay-5 btn-primary">
            Pinjam Buku
        </button>
    </div>
</main>



<script>
// Load buku ke select dropdown
function loadBukuSelect(){
    axios.get('api.php?action=get_buku')
    .then(res=>{
        const select = document.getElementById('bukuSelect');
        select.innerHTML = '';
        res.data.forEach(buku=>{
            select.innerHTML += `<option value="${buku.id}" ${buku.stok==0?'disabled':''}>
                ${buku.judul} (Stok: ${buku.stok})
            </option>`;
        });
    })
    .catch(err=>console.error("Gagal load buku:", err));
}

// Fungsi pinjam buku
function pinjamBuku(){
    const id_buku = document.getElementById('bukuSelect').value;
    const nama = document.getElementById('namaPeminjam').value.trim();

    if(!nama){
        alert('Masukkan nama peminjam!');
        return;
    }

    // Gunakan FormData agar PHP $_POST terbaca
    const formData = new FormData();
    formData.append('id_buku', id_buku);
    formData.append('nama', nama);

    axios.post('api.php?action=pinjam_buku', formData)
    .then(res=>{
        if(res.data.status==1){
            alert('Berhasil meminjam buku');
            document.getElementById('namaPeminjam').value='';
            loadBukuSelect(); // refresh daftar buku
        } else {
            alert('Gagal meminjam buku: ' + (res.data.error || 'Unknown error'));
            console.log(res.data);
        }
    })
    .catch(err=>{
        console.error("Error pinjam buku:", err);
        alert('Terjadi kesalahan saat meminjam buku');
    });
}

// Load buku saat halaman siap
document.addEventListener('DOMContentLoaded', loadBukuSelect);
</script>
</body>
</html>
