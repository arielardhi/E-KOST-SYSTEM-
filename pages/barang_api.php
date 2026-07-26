<?php include '../layouts/header.php'; ?>

<script>
    window.BASE_URL = '<?php echo $base_url; ?>';
</script>

<style>
#react-root { min-height: 500px; }
.nb-react-wrap * { box-sizing: border-box; }
</style>

<script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="container-fluid px-3 px-md-4 nb-react-wrap">
    <!-- Page Header — tema biru sesuai halaman utama -->
    <div class="card bg-primary text-white p-4 mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary) 0%, #312e81 100%) !important;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 style="font-size:clamp(1.4rem,4vw,2rem);font-weight:800;letter-spacing:-0.02em;margin:0;">Katalog Barang Kebutuhan Kos</h1>
                <div style="font-size:.85rem;opacity:.9;margin-top:6px;">Temukan semua kebutuhan kos kamu di satu tempat</div>
            </div>

        </div>
    </div>
    <div id="react-root"></div>
</div>

<script type="text/babel">
const { useState, useEffect, useCallback, useMemo } = React;

// ─── MOCK DATA — 15 Barang Kebutuhan Kos ─────────────
const MOCK_BARANG = [
    {
        id: 1,
        nama_barang: 'Kasur Lipat Busa Single 8cm',
        kategori: 'Furnitur', icon: '🛏️',
        harga: 285000,
        kondisi: 'Baru', stok: 5,
        kota: 'Yogyakarta', rating: 4.8, jumlah_review: 32,
        deskripsi: 'Kasur lipat busa high-density tebal 8cm, nyaman untuk tidur sehari-hari. Ukuran 90x200cm, cover waterproof anti-air, mudah dilipat dan disimpan di bawah ranjang.',
        seller_name: 'Toko Furnitur Jaya', seller_phone: '081234567890',
        tags: ['Lipat','Waterproof','90x200cm'],
        img: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 2,
        nama_barang: 'Lemari Pakaian Plastik 3 Pintu',
        kategori: 'Furnitur', icon: '🗄️',
        harga: 395000,
        kondisi: 'Baru', stok: 3,
        kota: 'Semarang', rating: 4.5, jumlah_review: 18,
        deskripsi: 'Lemari plastik ringan 3 pintu dengan 6 rak penyimpanan. Mudah dirakit tanpa alat khusus. Tahan hingga 30kg, cocok untuk kamar kos sempit.',
        seller_name: 'Griya Plastik', seller_phone: '082233445566',
        tags: ['3 Pintu','Ringan','Mudah Rakit'],
        img: 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 3,
        nama_barang: 'Meja Belajar Lipat Portable',
        kategori: 'Furnitur', icon: '📚',
        harga: 165000,
        kondisi: 'Baru', stok: 10,
        kota: 'Bandung', rating: 4.7, jumlah_review: 45,
        deskripsi: 'Meja belajar lipat multifungsi, bisa dipakai di lantai maupun di atas kasur. Permukaan MDF anti-gores, kaki aluminium kokoh, tahan beban 15kg. Berat hanya 1.8kg.',
        seller_name: 'Study Corner', seller_phone: '083344556677',
        tags: ['Portable','Lantai/Kasur','1.8kg'],
        img: 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 4,
        nama_barang: 'Rice Cooker Mini 0.8L Cosmos',
        kategori: 'Peralatan Dapur', icon: '🍚',
        harga: 178000,
        kondisi: 'Baru', stok: 8,
        kota: 'Yogyakarta', rating: 4.6, jumlah_review: 61,
        deskripsi: 'Rice cooker mini kapasitas 0.8 liter, ideal untuk 1–2 porsi. Hemat listrik hanya 200W. Dilengkapi spatula, gelas takaran, dan tutup kaca anti-panas.',
        seller_name: 'Elektronik Murah', seller_phone: '085566778899',
        tags: ['0.8L','200W','Hemat Listrik'],
        img: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 5,
        nama_barang: 'Kipas Angin Meja USB 5V',
        kategori: 'Elektronik', icon: '🌀',
        harga: 79000,
        kondisi: 'Baru', stok: 15,
        kota: 'Jakarta', rating: 4.4, jumlah_review: 87,
        deskripsi: 'Kipas meja bertenaga USB 5V, bisa dicolok ke laptop, charger, atau powerbank. 3 kecepatan angin, suara senyap di bawah 35dB. Kepala bisa diputar 360°.',
        seller_name: 'Gadget Kos', seller_phone: '087788990011',
        tags: ['USB 5V','360°','35dB'],
        img: 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 6,
        nama_barang: 'Rak Sepatu Besi 3 Susun',
        kategori: 'Furnitur', icon: '👟',
        harga: 89000,
        kondisi: 'Baru', stok: 12,
        kota: 'Surabaya', rating: 4.3, jumlah_review: 29,
        deskripsi: 'Rak sepatu 3 susun dari besi anti-karat dengan lapisan powder-coat. Menampung 9 pasang sepatu. Dirakit tanpa baut hanya dalam 5 menit.',
        seller_name: 'Rak Serba Ada', seller_phone: '088899001122',
        tags: ['Anti-karat','9 Pasang','5 Menit Rakit'],
        img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 7,
        nama_barang: 'Paket Perlengkapan Mandi Kos',
        kategori: 'Perlengkapan Mandi', icon: '🧴',
        harga: 68000,
        kondisi: 'Baru', stok: 20,
        kota: 'Bandung', rating: 4.9, jumlah_review: 104,
        deskripsi: 'Paket starter lengkap: ember 12L, gayung, sabun cair 250ml, shampo sachet 10pcs, sikat gigi, pasta gigi, dan handuk microfiber 50x100cm.',
        seller_name: 'Kos Starter Pack', seller_phone: '089900112233',
        tags: ['Starter Pack','Microfiber','All-in-One'],
        img: 'https://images.unsplash.com/photo-1600857544200-b2f666a9a2ec?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 8,
        nama_barang: 'Dispenser Air Mini Desktop',
        kategori: 'Peralatan Dapur', icon: '💧',
        harga: 265000,
        kondisi: 'Baru', stok: 4,
        kota: 'Malang', rating: 4.6, jumlah_review: 38,
        deskripsi: 'Dispenser air panas & dingin ukuran mini untuk meja. Kompatibel botol galon 5L/10L/15L/19L. Konsumsi listrik 500W (panas) / 65W (pendingin).',
        seller_name: 'Air Bersih Store', seller_phone: '081122334455',
        tags: ['Panas & Dingin','500W','Galon 5–19L'],
        img: 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 9,
        nama_barang: 'Lampu LED Belajar Touch Dimmer',
        kategori: 'Elektronik', icon: '💡',
        harga: 115000,
        kondisi: 'Baru', stok: 9,
        kota: 'Yogyakarta', rating: 4.8, jumlah_review: 55,
        deskripsi: 'Lampu LED meja dengan sensor sentuh, 3 mode cahaya (putih/hangat/natural) dan 5 level kecerahan. Baterai built-in 2000mAh isi ulang via USB-C, tahan 6–8 jam.',
        seller_name: 'Cahaya Belajar', seller_phone: '082233445566',
        tags: ['Touch','USB-C','2000mAh'],
        img: 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 10,
        nama_barang: 'Karpet Lipat Busa 100x150cm',
        kategori: 'Dekorasi', icon: '🟫',
        harga: 138000,
        kondisi: 'Baru', stok: 7,
        kota: 'Semarang', rating: 4.5, jumlah_review: 22,
        deskripsi: 'Karpet lipat busa tebal 2cm ukuran 100x150cm. Bagian bawah anti-slip latex, bagian atas bulu lembut. Mudah dicuci dengan air, cepat kering.',
        seller_name: 'Home Comfort', seller_phone: '083355667788',
        tags: ['Anti-slip','100x150cm','Mudah Cuci'],
        img: 'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 11,
        nama_barang: 'Cermin Dinding Minimalis 30x60cm',
        kategori: 'Dekorasi', icon: '🪞',
        harga: 105000,
        kondisi: 'Baru', stok: 6,
        kota: 'Surabaya', rating: 4.7, jumlah_review: 41,
        deskripsi: 'Cermin dinding minimalis ukuran 30x60cm dengan bingkai kayu MDF warna hitam. Kaca 5mm anti-pecah. Dilengkapi gantungan dinding + sekrup, siap pasang.',
        seller_name: 'Dekor Kamar', seller_phone: '085577889900',
        tags: ['30x60cm','5mm','Bingkai Kayu'],
        img: 'https://images.unsplash.com/photo-1618220179428-22790b461013?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 12,
        nama_barang: 'Bantal & Guling Set Dacron',
        kategori: 'Perlengkapan Tidur', icon: '🛌',
        harga: 95000,
        kondisi: 'Baru', stok: 11,
        kota: 'Yogyakarta', rating: 4.9, jumlah_review: 90,
        deskripsi: 'Set 1 bantal + 1 guling isi serat dacron premium. Cover 100% katun percale, adem, tidak mudah kempes. Ukuran bantal 45x65cm, guling 20x60cm.',
        seller_name: 'Toko Tidur Nyaman', seller_phone: '086688990011',
        tags: ['Dacron','Katun 100%','Anti-kempes'],
        img: 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 13,
        nama_barang: 'Setrika Mini Travel 350W',
        kategori: 'Elektronik', icon: '👔',
        harga: 85000,
        kondisi: 'Baru', stok: 13,
        kota: 'Bandung', rating: 4.4, jumlah_review: 34,
        deskripsi: 'Setrika mini 350W, ringan hanya 380g. Pelat keramik anti-lengket, panas merata dalam 30 detik. Cocok untuk kos, cukup untuk kemeja, kaos, dan celana harian.',
        seller_name: 'Elektronik Hemat', seller_phone: '087799001122',
        tags: ['380g','Keramik','30 Detik Panas'],
        img: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 14,
        nama_barang: 'Kotak Penyimpanan Bertutup 5pcs',
        kategori: 'Penyimpanan', icon: '📦',
        harga: 72000,
        kondisi: 'Baru', stok: 18,
        kota: 'Malang', rating: 4.6, jumlah_review: 47,
        deskripsi: 'Set 5 kotak penyimpanan bertutup dari plastik PP food-grade BPA-free. Ukuran bervariasi: 2L × 2pcs, 1L × 2pcs, 0.5L × 1pc. Bisa ditumpuk hemat ruang.',
        seller_name: 'Plastik Prima', seller_phone: '088801122334',
        tags: ['BPA-Free','5 Ukuran','Ditumpuk'],
        img: 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=400&h=280&fit=crop&q=80',
    },
    {
        id: 15,
        nama_barang: 'Gantungan Baju Over Door 5 Hook',
        kategori: 'Perlengkapan Mandi', icon: '🪝',
        harga: 45000,
        kondisi: 'Baru', stok: 25,
        kota: 'Jakarta', rating: 4.3, jumlah_review: 63,
        deskripsi: 'Gantungan baju 5 hook dipasang di atas pintu tanpa perlu bor atau paku. Material besi anti-karat, tahan beban 15kg. Cocok untuk handuk, jaket, tas, dan baju.',
        seller_name: 'Hook & Hanger', seller_phone: '089912345678',
        tags: ['Tanpa Bor','5 Hook','15kg'],
        img: 'https://images.unsplash.com/photo-1558618047-f4e80fdb9bb5?w=400&h=280&fit=crop&q=80',
    },
];

const KATEGORI = [...new Set(MOCK_BARANG.map(b => b.kategori))].map(k => ({
    nama: k,
    icon: MOCK_BARANG.find(b => b.kategori === k)?.icon || '📦',
    total: MOCK_BARANG.filter(b => b.kategori === k).length,
}));

const KOTA = [...new Set(MOCK_BARANG.map(b => b.kota))].map(k => ({
    kota: k,
    total: MOCK_BARANG.filter(b => b.kota === k).length,
}));

// ─── Warna tema utama (sesuai style.css) ─────────────
const C = {
    blue:   '#4f46e5',
    yellow: '#f59e0b',
    orange: '#ea580c',
    pink:   '#db2777',
    green:  '#10b981',
    cyan:   '#0ea5e9',
    black:  '#1e293b',
    white:  '#ffffff',
    bg:     '#f8fafc',
    border: '#e2e8f0',
};

// ─── Format Rupiah ────────────────────────────────────
function rupiah(n) {
    if (!n) return 'Hubungi Penjual';
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

// ─── Stars ────────────────────────────────────────────
function Stars({ rating }) {
    const r = Math.round(rating || 0);
    return <span style={{color:C.yellow, letterSpacing:1}}>{'★'.repeat(r)}{'☆'.repeat(5-r)}</span>;
}

// ─── Loading ──────────────────────────────────────────
function Loading() {
    return (
        <div style={{display:'flex',flexDirection:'column',alignItems:'center',padding:'80px 0',gap:16}}>
            <div style={{width:50,height:50,border:`4px solid ${C.border}`,borderTop:`4px solid ${C.blue}`,borderRadius:'50%',animation:'spin .8s linear infinite'}}/>
            <div style={{fontWeight:700,fontSize:'1rem',textTransform:'uppercase',letterSpacing:1}}>Memuat Katalog Barang...</div>
            <div style={{fontSize:'.82rem',color:'#64748b'}}>Mengambil data barang kebutuhan kos...</div>
            <style>{`@keyframes spin{to{transform:rotate(360deg)}}`}</style>
        </div>
    );
}



// ─── Filter Bar ───────────────────────────────────────
function FilterBar({ filters, setFilters, onSearch }) {
    const [local, setLocal] = useState(filters);

    function handleSubmit(e) {
        e.preventDefault();
        setFilters(local);
        onSearch(local);
    }
    function handleReset() {
        const empty = { kategori:'', kota:'', min_price:'', max_price:'', kondisi:'', search:'' };
        setLocal(empty); setFilters(empty); onSearch(empty);
    }

    const inputStyle = {
        width:'100%', border:`1px solid ${C.border}`, padding:'9px 12px',
        fontWeight:600, fontSize:'.88rem',
        outline:'none', background:C.white, borderRadius:8,
    };
    const labelStyle = {
        fontWeight:700, fontSize:'.65rem',
        textTransform:'uppercase', letterSpacing:'1px', display:'block', marginBottom:5,
        color: '#475569',
    };

    return (
        <form onSubmit={handleSubmit} style={{border:`1px solid ${C.border}`,background:C.white,padding:'20px 22px',marginBottom:20,borderRadius:12,boxShadow:'var(--box-shadow)'}}>
            <div style={{fontSize:'.75rem',fontWeight:700,textTransform:'uppercase',letterSpacing:1,marginBottom:14,paddingBottom:10,borderBottom:`1px solid ${C.border}`,color:C.blue,display:'flex',alignItems:'center',gap:8}}>
                <span style={{background:C.blue,color:'#fff',padding:'2px 10px',fontSize:'.65rem',borderRadius:4}}>🔍 FILTER</span>
                Pencarian Barang Kebutuhan Kos
            </div>
            <div style={{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(160px,1fr))',gap:12,marginBottom:14}}>

                <div>
                    <label style={labelStyle}>🔎 Cari Barang</label>
                    <input type="text" placeholder="Kasur, kipas, meja..." value={local.search}
                        onChange={e=>setLocal({...local,search:e.target.value})} style={inputStyle} />
                </div>

                <div>
                    <label style={labelStyle}>📦 Kategori</label>
                    <select value={local.kategori} onChange={e=>setLocal({...local,kategori:e.target.value})} style={{...inputStyle,cursor:'pointer'}}>
                        <option value="">Semua Kategori</option>
                        {KATEGORI.map(c => <option key={c.nama} value={c.nama}>{c.icon} {c.nama} ({c.total})</option>)}
                    </select>
                </div>

                <div>
                    <label style={labelStyle}>📍 Kota</label>
                    <select value={local.kota} onChange={e=>setLocal({...local,kota:e.target.value})} style={{...inputStyle,cursor:'pointer'}}>
                        <option value="">Semua Kota</option>
                        {KOTA.map(c => <option key={c.kota} value={c.kota}>{c.kota} ({c.total})</option>)}
                    </select>
                </div>

                <div>
                    <label style={labelStyle}>✨ Kondisi</label>
                    <select value={local.kondisi} onChange={e=>setLocal({...local,kondisi:e.target.value})} style={{...inputStyle,cursor:'pointer'}}>
                        <option value="">Semua Kondisi</option>
                        <option value="Baru">🟢 Baru</option>
                        <option value="Bekas - Sangat Baik">🟡 Bekas - Sangat Baik</option>
                        <option value="Bekas - Baik">🟠 Bekas - Baik</option>
                    </select>
                </div>

                <div>
                    <label style={labelStyle}>💰 Harga Min (Rp)</label>
                    <input type="number" placeholder="50.000" value={local.min_price}
                        onChange={e=>setLocal({...local,min_price:e.target.value})} style={inputStyle} />
                </div>

                <div>
                    <label style={labelStyle}>💰 Harga Max (Rp)</label>
                    <input type="number" placeholder="500.000" value={local.max_price}
                        onChange={e=>setLocal({...local,max_price:e.target.value})} style={inputStyle} />
                </div>
            </div>

            <div style={{display:'flex',gap:8}}>
                <button type="submit" class="btn btn-primary" style={{flex:1}}>
                    🔍 Cari Barang
                </button>
                <button type="button" onClick={handleReset} class="btn btn-secondary">
                    ✕ Reset
                </button>
            </div>
        </form>
    );
}

// ─── Barang Card ──────────────────────────────────────
function BarangCard({ barang, onSelect }) {
    const [hovered, setHovered] = useState(false);
    const [imgErr, setImgErr]   = useState(false);

    const kondisiMap = {
        'Baru':                 ['rgba(16, 185, 129, 0.1)',  C.green],
        'Bekas - Sangat Baik':  ['rgba(245, 158, 11, 0.1)', C.yellow],
        'Bekas - Baik':         ['rgba(234, 88, 12, 0.1)', C.orange],
        'Bekas - Cukup Baik':   ['rgba(100, 116, 139, 0.1)',   '#64748b'],
    };
    const [badgeBg, badgeCol] = kondisiMap[barang.kondisi] || ['#e2e8f0', '#64748b'];

    return (
        <div
            onClick={() => onSelect(barang)}
            onMouseEnter={() => setHovered(true)}
            onMouseLeave={() => setHovered(false)}
            style={{
                border:`1px solid ${C.border}`,
                boxShadow: hovered ? 'var(--box-shadow-hover)' : 'var(--box-shadow)',
                background: C.white, cursor:'pointer',
                transform: hovered ? 'translateY(-4px)' : 'none',
                transition:'all .2s ease', display:'flex', flexDirection:'column', height:'100%',
                borderRadius: 12, overflow: 'hidden',
            }}
        >
            {/* Gambar */}
            <div style={{position:'relative',borderBottom:`1px solid ${C.border}`,height:185,background:'#f8fafc',overflow:'hidden'}}>
                {!imgErr ? (
                    <img
                        src={barang.img} alt={barang.nama_barang}
                        onError={() => setImgErr(true)}
                        style={{width:'100%',height:'100%',objectFit:'cover',display:'block',transition:'transform .3s'}}
                        onMouseEnter={e => e.currentTarget.style.transform='scale(1.05)'}
                        onMouseLeave={e => e.currentTarget.style.transform='scale(1)'}
                    />
                ) : (
                    <div style={{display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',width:'100%',height:'100%',background:'#f8fafc'}}>
                        <span style={{fontSize:'3.5rem'}}>{barang.icon}</span>
                        <span style={{fontSize:'.65rem',color:'#94a3b8',textTransform:'uppercase',fontWeight:700,marginTop:4}}>{barang.kategori}</span>
                    </div>
                )}

                {/* Overlay kategori kiri bawah */}
                <span class="badge bg-primary" style={{position:'absolute',bottom:8,left:8,boxShadow:'0 2px 4px rgba(0,0,0,0.1)'}}>
                    {barang.icon} {barang.kategori}
                </span>

                {/* Kondisi badge kanan atas */}
                <span class="badge" style={{position:'absolute',top:8,right:8,background:badgeBg,color:badgeCol,boxShadow:'0 2px 4px rgba(0,0,0,0.1)'}}>
                    {barang.kondisi}
                </span>
            </div>

            {/* Body */}
            <div style={{padding:'16px',flex:1,display:'flex',flexDirection:'column',gap:8}}>
                <h3 style={{fontSize:'0.95rem',fontWeight:700,margin:0,lineHeight:1.3,display:'-webkit-box',WebkitLineClamp:2,WebkitBoxOrient:'vertical',overflow:'hidden',color:'#0f172a'}}>
                    {barang.nama_barang}
                </h3>

                <div style={{fontWeight:600,fontSize:'.78rem',color:'#64748b'}}>
                    📍 {barang.kota}
                </div>

                {/* Stok */}
                <div>
                    {barang.stok > 0
                        ? <span class="badge bg-success">✓ {barang.stok} Stok</span>
                        : <span class="badge bg-danger">✗ Habis</span>
                    }
                </div>

                {/* Tags */}
                <div style={{display:'flex',gap:4,flexWrap:'wrap'}}>
                    {(barang.tags||[]).slice(0,3).map(t => (
                        <span key={t} class="badge bg-secondary" style={{fontSize:'0.65rem'}}>
                            {t}
                        </span>
                    ))}
                </div>

                {/* Rating */}
                {barang.rating && (
                    <div style={{display:'flex',alignItems:'center',gap:6,marginTop:2}}>
                        <Stars rating={barang.rating}/>
                        <span style={{fontWeight:700,fontSize:'.72rem',color:'#64748b'}}>{barang.rating} ({barang.jumlah_review})</span>
                    </div>
                )}

                {/* Harga */}
                <div style={{marginTop:'auto',paddingTop:12,borderTop:`1px solid #f1f5f9`}}>
                    <div style={{fontWeight:800,fontSize:'1.15rem',letterSpacing:'-0.5px',color:C.blue}}>
                        {rupiah(barang.harga)}
                    </div>
                </div>
            </div>

            {/* CTA */}
            <div style={{padding:'0 16px 16px'}}>
                <button class="btn btn-outline-primary btn-sm w-100" style={{padding:'8px'}}>
                    Lihat Detail
                </button>
            </div>
        </div>
    );
}

// ─── Modal Detail Barang ──────────────────────────────
function BarangModal({ barang, onClose }) {
    if (!barang) return null;

    const kondisiMap = {
        'Baru':                ['rgba(16, 185, 129, 0.1)', C.green],
        'Bekas - Sangat Baik': ['rgba(245, 158, 11, 0.1)', C.yellow],
        'Bekas - Baik':        ['rgba(234, 88, 12, 0.1)',  C.orange],
        'Bekas - Cukup Baik':  ['rgba(100, 116, 139, 0.1)',   '#64748b'],
    };
    const [bg, col] = kondisiMap[barang.kondisi] || ['#e2e8f0','#64748b'];

    let waPhone = (barang.seller_phone||'').replace(/\D/g,'');
    if (waPhone.startsWith('0')) waPhone = '62' + waPhone.slice(1);
    const waMsg = encodeURIComponent(`Halo kak, saya tertarik dengan *${barang.nama_barang}* seharga ${rupiah(barang.harga)}. Apakah masih tersedia?`);

    return (
        <div onClick={onClose} style={{position:'fixed',inset:0,background:'rgba(15, 23, 42, 0.6)',zIndex:9999,display:'flex',alignItems:'center',justifyContent:'center',padding:16,overflowY:'auto',backdropFilter:'blur(4px)'}}>
            <div onClick={e=>e.stopPropagation()} style={{background:C.white,border:`1px solid ${C.border}`,boxShadow:'0 25px 50px -12px rgba(0,0,0,0.25)',maxWidth:600,width:'100%',maxHeight:'92vh',overflow:'auto',position:'relative',borderRadius:16}}>

                {/* Header modal */}
                <div style={{background:C.blue,color:C.white,padding:'20px 24px',display:'flex',alignItems:'center',justifyContent:'space-between',position:'sticky',top:0,zIndex:2}}>
                    <div>
                        <div style={{fontSize:'.7rem',fontWeight:700,textTransform:'uppercase',letterSpacing:1.5,color:'#a5b4fc',marginBottom:3}}>
                            {barang.icon} {barang.kategori} · 📍 {barang.kota}
                        </div>
                        <div style={{fontSize:'1.25rem',fontWeight:800,letterSpacing:'-0.5px',lineHeight:1.2,color:C.white}}>
                            {barang.nama_barang}
                        </div>
                    </div>
                    <button onClick={onClose} class="btn-close btn-close-white" style={{background:'none',border:'none',fontSize:'1.25rem',color:'#fff',cursor:'pointer'}}><i class="bi bi-x-lg"></i></button>
                </div>

                <div style={{padding:'24px'}}>
                    {/* Gambar */}
                    <div style={{height:240,borderRadius:12,overflow:'hidden',background:'#f8fafc',marginBottom:20,border:`1px solid ${C.border}`}}>
                        <img src={barang.img} alt={barang.nama_barang}
                            style={{width:'100%',height:'100%',objectFit:'cover',display:'block'}}
                            onError={e=>{e.target.style.display='none'; e.target.nextSibling.style.display='flex'}}
                        />
                        <div style={{display:'none',width:'100%',height:'100%',alignItems:'center',justifyContent:'center',flexDirection:'column',gap:6}}>
                            <span style={{fontSize:'5rem'}}>{barang.icon}</span>
                            <span style={{fontSize:'.65rem',color:'#94a3b8',textTransform:'uppercase',fontWeight:700}}>{barang.kategori}</span>
                        </div>
                    </div>

                    {/* Badge row */}
                    <div style={{display:'flex',gap:8,flexWrap:'wrap',marginBottom:16}}>
                        <span class="badge" style={{background:bg,color:col}}>{barang.kondisi}</span>
                        {barang.rating && (
                            <span class="badge bg-warning text-dark">
                                ⭐ {barang.rating}/5 &nbsp;({barang.jumlah_review} ulasan)
                            </span>
                        )}
                        <span class="badge bg-success">
                            {barang.stok>0 ? `✓ ${barang.stok} Tersedia` : '✗ Habis'}
                        </span>
                    </div>

                    {/* Tags */}
                    <div style={{display:'flex',gap:6,flexWrap:'wrap',marginBottom:20}}>
                        {(barang.tags||[]).map(t => (
                            <span key={t} class="badge bg-secondary" style={{fontSize:'0.65rem'}}>
                                # {t}
                            </span>
                        ))}
                    </div>

                    {/* Deskripsi */}
                    <div style={{fontSize:'.9rem',lineHeight:1.7,color:'#475569',marginBottom:20,padding:'16px',background:'#f8fafc',border:`1px solid ${C.border}`,borderRadius:12}}>
                        {barang.deskripsi}
                    </div>

                    {/* Harga box */}
                    <div style={{background: 'linear-gradient(135deg, var(--primary) 0%, #312e81) !important', color:C.white,padding:'16px 20px',marginBottom:20,display:'flex',alignItems:'center',justifyContent:'space-between',flexWrap:'wrap',gap:8,borderRadius:12,backgroundColor:C.blue}}>
                        <div>
                            <div style={{fontSize:'.65rem',fontWeight:700,textTransform:'uppercase',letterSpacing:1,color:'#c7d2fe',marginBottom:2}}>Harga Barang</div>
                            <div style={{fontSize:'1.8rem',fontWeight:800,letterSpacing:'-0.5px',lineHeight:1,color:'#ffffff'}}>{rupiah(barang.harga)}</div>
                        </div>
                        <div style={{fontWeight:700,fontSize:'.82rem',color:'#c7d2fe'}}>
                            📍 {barang.kota}
                        </div>
                    </div>

                    {/* Penjual */}
                    {barang.seller_name && (
                        <div style={{border:`1px solid ${C.border}`,background:'#f8fafc',padding:'16px',marginBottom:24,borderRadius:12}}>
                            <div style={{fontSize:'.65rem',fontWeight:700,textTransform:'uppercase',letterSpacing:1,marginBottom:8,color:'#64748b'}}>👤 Info Penjual</div>
                            <div style={{fontWeight:700,fontSize:'.95rem',color:'#0f172a',marginBottom:3}}>{barang.seller_name}</div>
                            {barang.seller_phone && (
                                <div style={{fontSize:'.82rem',color:'#64748b'}}><i class="bi bi-telephone-fill me-1"></i> {barang.seller_phone}</div>
                            )}
                        </div>
                    )}

                    {/* CTA buttons */}
                    <div style={{display:'flex',gap:8}}>
                        {waPhone && (
                            <a href={`https://wa.me/${waPhone}?text=${waMsg}`} target="_blank" rel="noopener noreferrer" class="btn btn-success"
                                style={{flex:2, padding:'12px', display:'flex', alignItems:'center', justifyContent:'center', gap:8}}>
                                <i class="bi bi-whatsapp"></i> Hubungi Penjual
                            </a>
                        )}
                        <button onClick={onClose} class="btn btn-secondary" style={{flex:1}}>
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}



// ─── Main App ─────────────────────────────────────────
function BarangApp() {
    const [loading, setLoading]           = useState(true);
    const [selectedBarang, setSelectedBarang] = useState(null);
    const [activeFilters, setActiveFilters]   = useState({ kategori:'', kota:'', min_price:'', max_price:'', kondisi:'', search:'' });
    const [filters, setFilters]           = useState({ kategori:'', kota:'', min_price:'', max_price:'', kondisi:'', search:'' });

    useEffect(() => {
        const t = setTimeout(() => setLoading(false), 500);
        return () => clearTimeout(t);
    }, []);

    const filtered = useMemo(() => MOCK_BARANG.filter(b => {
        const f = activeFilters;
        if (f.search    && !b.nama_barang.toLowerCase().includes(f.search.toLowerCase()) && !b.deskripsi.toLowerCase().includes(f.search.toLowerCase())) return false;
        if (f.kategori  && b.kategori !== f.kategori)  return false;
        if (f.kota      && b.kota !== f.kota)          return false;
        if (f.kondisi   && b.kondisi !== f.kondisi)    return false;
        if (f.min_price && b.harga < +f.min_price)     return false;
        if (f.max_price && b.harga > +f.max_price)     return false;
        return true;
    }), [activeFilters]);

    if (loading) return <Loading />;

    return (
        <>

            <FilterBar filters={filters} setFilters={setFilters} onSearch={setActiveFilters} />

            {filtered.length === 0 ? (
                <div style={{textAlign:'center',padding:'70px 20px',border:`1px solid ${C.border}`,background:C.white,borderRadius:12,boxShadow:'var(--box-shadow)'}}>
                    <div style={{fontSize:'3rem',marginBottom:12}}>🔍</div>
                    <div style={{fontWeight:700,fontSize:'1.1rem',textTransform:'uppercase',letterSpacing:1,color:'#0f172a'}}>Tidak Ada Barang Ditemukan</div>
                    <div style={{fontSize:'.9rem',color:'#64748b',marginTop:6}}>Coba ubah kata kunci atau hapus filter</div>
                </div>
            ) : (
                <>
                    <div style={{fontSize:'.75rem',fontWeight:700,textTransform:'uppercase',letterSpacing:1,marginBottom:14,color:'#64748b',display:'flex',alignItems:'center',gap:8}}>
                        <span class="badge bg-primary">
                            {filtered.length} BARANG
                        </span>
                        Kebutuhan Kos Tersedia
                    </div>
                    <div style={{display:'grid',gridTemplateColumns:'repeat(auto-fill,minmax(230px,1fr))',gap:18,marginBottom:48}}>
                        {filtered.map(b => <BarangCard key={b.id} barang={b} onSelect={setSelectedBarang} />)}
                    </div>
                </>
            )}

            <BarangModal barang={selectedBarang} onClose={() => setSelectedBarang(null)} />
        </>
    );
}

ReactDOM.render(<BarangApp />, document.getElementById('react-root'));
</script>

<?php include '../layouts/footer.php'; ?>
