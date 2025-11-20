function loadBuku(){
    axios.get('api.php?action=get_buku')
    .then(res=>{
        const list = document.getElementById('listBuku');
        list.innerHTML = '';
        res.data.forEach(buku=>{
            list.innerHTML += `
            <div class="card">
                <h4>${buku.judul}</h4>
                <p>Penulis: ${buku.penulis}</p>
                <p>Tahun: ${buku.tahun}</p>
                <p>Stok: ${buku.stok}</p>
                <button onclick="hapusBuku(${buku.id})">Hapus</button>
            </div>`;
        });
    });
}

function loadPeminjaman(){
    axios.get('api.php?action=get_peminjaman')
    .then(res=>{
        const list = document.getElementById('listPeminjaman');
        list.innerHTML = '';
        res.data.forEach(pinjam=>{
            list.innerHTML += `
            <div class="card">
                <h4>${pinjam.judul}</h4>
                <p>Peminjam: ${pinjam.nama_peminjam}</p>
                <p>Tanggal Pinjam: ${pinjam.tanggal_pinjam}</p>
                <p>Status: ${pinjam.status}</p>
                ${pinjam.status==='Dipinjam'? `<button onclick="kembalikanBuku(${pinjam.id})">Kembalikan</button>`:``}
            </div>`;
        });
    });
}

function showTambahBuku(){
    document.getElementById('modalBuku').style.display = 'block';
}

function closeModal(){
    document.getElementById('modalBuku').style.display = 'none';
}

function tambahBuku(){
    const judul = document.getElementById('judul').value;
    const penulis = document.getElementById('penulis').value;
    const tahun = document.getElementById('tahun').value;
    const stok = document.getElementById('stok').value;

    const formData = new FormData();
    formData.append('judul', judul);
    formData.append('penulis', penulis);
    formData.append('tahun', tahun);
    formData.append('stok', stok);

    axios.post('api.php?action=tambah_buku', formData)
        .then(res=>{
            if(res.data.status==1){
                loadBuku();
                closeModal();
            } else alert('Gagal tambah buku');
        });
}

list.innerHTML += `<div class="card">
    <h4>${buku.judul}</h4>
    <p>Penulis: ${buku.penulis}</p>
    <p>Tahun: ${buku.tahun}</p>
    <p>Stok: ${buku.stok}</p>
    <button onclick="showEditBuku(${buku.id},'${buku.judul}','${buku.penulis}',${buku.tahun},${buku.stok})">Edit</button>
    <button onclick="hapusBuku(${buku.id})">Hapus</button>
</div>`;

// Tampilkan modal edit dengan data buku
function showEditBuku(id, judul, penulis, tahun, stok){
    document.getElementById('editId').value = id;
    document.getElementById('editJudul').value = judul;
    document.getElementById('editPenulis').value = penulis;
    document.getElementById('editTahun').value = tahun;
    document.getElementById('editStok').value = stok;
    document.getElementById('modalEditBuku').style.display = 'block';
}

function closeEditModal(){
    document.getElementById('modalEditBuku').style.display = 'none';
}

// Proses edit buku dengan AJAX
function editBuku(){
    const id = document.getElementById('editId').value;
    const judul = document.getElementById('editJudul').value;
    const penulis = document.getElementById('editPenulis').value;
    const tahun = document.getElementById('editTahun').value;
    const stok = document.getElementById('editStok').value;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('judul', judul);
    formData.append('penulis', penulis);
    formData.append('tahun', tahun);
    formData.append('stok', stok);

    axios.post('api.php?action=edit_buku', formData)
    .then(res=>{
        if(res.data.status==1){
            loadBuku(); // refresh daftar buku
            closeEditModal();
        } else {
            alert('Gagal edit buku');
            console.log(res.data);
        }
    });
}


function hapusBuku(id){
    if(!confirm("Apakah yakin ingin menghapus buku ini?")) return;

    const formData = new FormData();
    formData.append('id', id);

    axios.post('api.php?action=hapus_buku', formData)
    .then(res=>{
        if(res.data.status==1){
            loadBuku(); // refresh daftar buku
        } else {
            alert("Gagal hapus buku");
            console.log(res.data);
        }
    });
}


function kembalikanBuku(id){
    axios.post('api.php?action=kembalikan_buku',{id})
    .then(res=>{if(res.data.status==1) loadPeminjaman()});
}

document.addEventListener('DOMContentLoaded', function() {
    loadBuku();
    loadPeminjaman();
});
