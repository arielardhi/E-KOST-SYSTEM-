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
    <div style="border:4px solid #000;box-shadow:8px 8px 0 #000;background:#001ee1;color:#fff;padding:22px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-family:'Archivo Black',sans-serif;font-size:.6rem;text-transform:uppercase;letter-spacing:2px;opacity:.7;margin-bottom:4px;">🛒 React + Axios · E-KOST Internal API</div>
            <h1 style="font-family:'Archivo Black',sans-serif;font-size:clamp(1.4rem,4vw,2.4rem);text-transform:uppercase;letter-spacing:-2px;line-height:1;margin:0;">Katalog <span style="color:#FFD600;">Barang Kebutuhan Kos</span></h1>
            <div style="font-family:'Space Grotesk',sans-serif;font-weight:600;font-size:.85rem;color:#a8bcff;margin-top:6px;">Temukan semua kebutuhan kos kamu di satu tempat</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="background:#FFD600;color:#000;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">⚛️ useState</span>
            <span style="background:#00FF94;color:#000;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">🔄 useEffect</span>
            <span style="background:#FF5C00;color:#fff;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">🌐 Mock API</span>
        </div>
    </div>
    <div id="react-root"></div>
</div>

<script type="text/babel">
const { useState, useEffect, useCallback, useMemo } = React;

// ─── MOCK DATA — 15 Barang Kebutuhan Kos ─────────────
// Gambar dari Unsplash (bebas pakai, relevan per produk)
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
    blue:   '#001ee1',
    yellow: '#FFD600',
    orange: '#FF5C00',
    pink:   '#FF3CAC',
    green:  '#00FF94',
    cyan:   '#00E0FF',
    black:  '#000000',
    white:  '#ffffff',
    bg:     '#f4f4f0',
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
            <div style={{width:56,height:56,border:`5px solid ${C.black}`,borderTop:`5px solid ${C.blue}`,borderRadius:'50%',animation:'spin .8s linear infinite',boxShadow:`4px 4px 0 ${C.black}`}}/>
            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',textTransform:'uppercase',letterSpacing:2}}>Memuat Katalog Barang...</div>
            <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.82rem',color:'#888'}}>Mengambil data barang kebutuhan kos...</div>
            <style>{`@keyframes spin{to{transform:rotate(360deg)}}`}</style>
        </div>
    );
}

// ─── Stats Bar ────────────────────────────────────────
function StatsBar({ total }) {
    const totalStok = MOCK_BARANG.reduce((a,b) => a + b.stok, 0);
    const minHarga  = Math.min(...MOCK_BARANG.map(b => b.harga));
    const items = [
        { label:'Total Barang',   value: MOCK_BARANG.length,   bg: C.blue,   color: C.yellow },
        { label:'Kategori',       value: KATEGORI.length,      bg: C.green,  color: C.black  },
        { label:'Stok Tersedia',  value: totalStok,            bg: C.cyan,   color: C.black  },
        { label:'Harga Mulai',    value: rupiah(minHarga),     bg: C.yellow, color: C.black  },
        { label:'Hasil Filter',   value: total,                bg: C.orange, color: C.white  },
    ];
    return (
        <div style={{display:'flex',gap:10,marginBottom:20,flexWrap:'wrap'}}>
            {items.map(s => (
                <div key={s.label} style={{background:s.bg,color:s.color,border:`3px solid ${C.black}`,boxShadow:`4px 4px 0 ${C.black}`,padding:'10px 16px',flex:'1 1 90px',minWidth:90}}>
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.6rem',textTransform:'uppercase',letterSpacing:1,opacity:.75}}>{s.label}</div>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.3rem',letterSpacing:'-1px',lineHeight:1.1}}>{s.value}</div>
                </div>
            ))}
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
        width:'100%', border:`3px solid ${C.black}`, padding:'9px 12px',
        fontFamily:'Space Grotesk,sans-serif', fontWeight:600, fontSize:'.88rem',
        outline:'none', background:C.white, borderRadius:0,
    };
    const labelStyle = {
        fontFamily:'Archivo Black,sans-serif', fontSize:'.65rem',
        textTransform:'uppercase', letterSpacing:'1px', display:'block', marginBottom:5,
    };

    const quickFilters = [
        {label:'≤ 100rb',     fn: f => ({...f, max_price:'100000'})},
        {label:'≤ 300rb',     fn: f => ({...f, max_price:'300000'})},
        {label:'≤ 500rb',     fn: f => ({...f, max_price:'500000'})},
        {label:'Barang Baru', fn: f => ({...f, kondisi:'Baru'})},
        {label:'Furnitur',    fn: f => ({...f, kategori:'Furnitur'})},
        {label:'Elektronik',  fn: f => ({...f, kategori:'Elektronik'})},
        {label:'Dapur',       fn: f => ({...f, kategori:'Peralatan Dapur'})},
    ];

    return (
        <form onSubmit={handleSubmit} style={{border:`4px solid ${C.black}`,boxShadow:`6px 6px 0 ${C.black}`,background:C.white,padding:'20px 22px',marginBottom:20}}>
            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.75rem',textTransform:'uppercase',letterSpacing:1,marginBottom:14,paddingBottom:10,borderBottom:`2px solid ${C.black}`,color:C.blue,display:'flex',alignItems:'center',gap:8}}>
                <span style={{background:C.blue,color:C.yellow,border:`2px solid ${C.black}`,padding:'2px 10px',fontSize:'.65rem'}}>🔍 FILTER</span>
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
                    <select value={local.kategori} onChange={e=>setLocal({...local,kategori:e.target.value})} style={{...inputStyle,cursor:'pointer',appearance:'none'}}>
                        <option value="">Semua Kategori</option>
                        {KATEGORI.map(c => <option key={c.nama} value={c.nama}>{c.icon} {c.nama} ({c.total})</option>)}
                    </select>
                </div>

                <div>
                    <label style={labelStyle}>📍 Kota</label>
                    <select value={local.kota} onChange={e=>setLocal({...local,kota:e.target.value})} style={{...inputStyle,cursor:'pointer',appearance:'none'}}>
                        <option value="">Semua Kota</option>
                        {KOTA.map(c => <option key={c.kota} value={c.kota}>{c.kota} ({c.total})</option>)}
                    </select>
                </div>

                <div>
                    <label style={labelStyle}>✨ Kondisi</label>
                    <select value={local.kondisi} onChange={e=>setLocal({...local,kondisi:e.target.value})} style={{...inputStyle,cursor:'pointer',appearance:'none'}}>
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

            {/* Quick Filters */}
            <div style={{display:'flex',gap:6,flexWrap:'wrap',marginBottom:14,alignItems:'center'}}>
                <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',letterSpacing:1,color:'#666'}}>Cepat →</span>
                {quickFilters.map(q => (
                    <button type="button" key={q.label}
                        onClick={() => { const n = q.fn(local); setLocal(n); setFilters(n); onSearch(n); }}
                        style={{background:C.bg,color:C.black,border:`2px solid ${C.black}`,padding:'5px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.62rem',textTransform:'uppercase',cursor:'pointer',boxShadow:`2px 2px 0 ${C.black}`,transition:'all .1s'}}>
                        {q.label}
                    </button>
                ))}
            </div>

            <div style={{display:'flex',gap:8}}>
                <button type="submit" style={{background:C.blue,color:C.yellow,border:`3px solid ${C.black}`,padding:'11px 28px',fontFamily:'Archivo Black,sans-serif',fontSize:'.85rem',textTransform:'uppercase',cursor:'pointer',boxShadow:`4px 4px 0 ${C.black}`,flex:1,letterSpacing:.5}}>
                    🔍 Cari Barang
                </button>
                <button type="button" onClick={handleReset} style={{background:'#eee',color:C.black,border:`3px solid ${C.black}`,padding:'11px 18px',fontFamily:'Archivo Black,sans-serif',fontSize:'.85rem',textTransform:'uppercase',cursor:'pointer',boxShadow:`4px 4px 0 ${C.black}`}}>
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
        'Baru':                 [C.green,  C.black],
        'Bekas - Sangat Baik':  [C.yellow, C.black],
        'Bekas - Baik':         [C.orange, C.white],
        'Bekas - Cukup Baik':   ['#888',   C.white],
    };
    const [badgeBg, badgeCol] = kondisiMap[barang.kondisi] || ['#999', C.white];

    return (
        <div
            onClick={() => onSelect(barang)}
            onMouseEnter={() => setHovered(true)}
            onMouseLeave={() => setHovered(false)}
            style={{
                border:`4px solid ${C.black}`,
                boxShadow: hovered ? `10px 10px 0 ${C.black}` : `6px 6px 0 ${C.black}`,
                background: C.white, cursor:'pointer',
                transform: hovered ? 'translate(-3px,-3px)' : 'none',
                transition:'all .12s', display:'flex', flexDirection:'column', height:'100%',
            }}
        >
            {/* Gambar */}
            <div style={{position:'relative',borderBottom:`3px solid ${C.black}`,height:185,background:'#e8eeff',overflow:'hidden'}}>
                {!imgErr ? (
                    <img
                        src={barang.img} alt={barang.nama_barang}
                        onError={() => setImgErr(true)}
                        style={{width:'100%',height:'100%',objectFit:'cover',display:'block',transition:'transform .3s'}}
                        onMouseEnter={e => e.currentTarget.style.transform='scale(1.05)'}
                        onMouseLeave={e => e.currentTarget.style.transform='scale(1)'}
                    />
                ) : (
                    <div style={{display:'flex',flexDirection:'column',alignItems:'center',justifyContent:'center',width:'100%',height:'100%',background:'#e8eeff'}}>
                        <span style={{fontSize:'3.5rem'}}>{barang.icon}</span>
                        <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',color:'#aaa',textTransform:'uppercase',marginTop:4}}>{barang.kategori}</span>
                    </div>
                )}

                {/* Overlay kategori kiri bawah */}
                <span style={{position:'absolute',bottom:8,left:8,background:C.blue,color:C.yellow,border:`2px solid ${C.black}`,padding:'3px 9px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',boxShadow:`2px 2px 0 ${C.black}`}}>
                    {barang.icon} {barang.kategori}
                </span>

                {/* Kondisi badge kanan atas */}
                <span style={{position:'absolute',top:8,right:8,background:badgeBg,color:badgeCol,border:`2px solid ${C.black}`,padding:'3px 9px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',boxShadow:`2px 2px 0 ${C.black}`}}>
                    {barang.kondisi}
                </span>
            </div>

            {/* Body */}
            <div style={{padding:'14px 16px',flex:1,display:'flex',flexDirection:'column',gap:6}}>
                <h3 style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',textTransform:'uppercase',letterSpacing:'-0.5px',margin:0,lineHeight:1.2,display:'-webkit-box',WebkitLineClamp:2,WebkitBoxOrient:'vertical',overflow:'hidden'}}>
                    {barang.nama_barang}
                </h3>

                <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.78rem',color:'#555'}}>
                    📍 {barang.kota}
                </div>

                {/* Stok */}
                <div>
                    {barang.stok > 0
                        ? <span style={{background:C.green,color:C.black,border:`1.5px solid ${C.black}`,padding:'2px 8px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase'}}>✓ {barang.stok} Stok</span>
                        : <span style={{background:'#FF4B4B',color:C.white,border:`1.5px solid ${C.black}`,padding:'2px 8px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase'}}>✗ Habis</span>
                    }
                </div>

                {/* Tags */}
                <div style={{display:'flex',gap:4,flexWrap:'wrap'}}>
                    {(barang.tags||[]).slice(0,3).map(t => (
                        <span key={t} style={{background:'#e8eeff',border:`1.5px solid ${C.black}`,padding:'2px 7px',fontFamily:'Archivo Black,sans-serif',fontSize:'.58rem',textTransform:'uppercase',letterSpacing:.3,color:C.blue}}>
                            {t}
                        </span>
                    ))}
                </div>

                {/* Rating */}
                {barang.rating && (
                    <div style={{display:'flex',alignItems:'center',gap:6}}>
                        <Stars rating={barang.rating}/>
                        <span style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.72rem',color:'#888'}}>{barang.rating} ({barang.jumlah_review})</span>
                    </div>
                )}

                {/* Harga */}
                <div style={{marginTop:'auto',paddingTop:10,borderTop:`2px solid #eee`}}>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.2rem',letterSpacing:'-1px',color:C.blue}}>
                        {rupiah(barang.harga)}
                    </div>
                </div>
            </div>

            {/* CTA */}
            <div style={{padding:'0 12px 12px'}}>
                <div style={{background:C.blue,color:C.yellow,border:`3px solid ${C.black}`,padding:'9px',fontFamily:'Archivo Black,sans-serif',fontSize:'.72rem',textTransform:'uppercase',textAlign:'center',boxShadow:`3px 3px 0 ${C.black}`,letterSpacing:.5}}>
                    Lihat Detail →
                </div>
            </div>
        </div>
    );
}

// ─── Modal Detail Barang ──────────────────────────────
function BarangModal({ barang, onClose }) {
    if (!barang) return null;

    const kondisiMap = {
        'Baru':                ['#00FF94','#000'],
        'Bekas - Sangat Baik': ['#FFD600','#000'],
        'Bekas - Baik':        ['#FF5C00','#fff'],
        'Bekas - Cukup Baik':  ['#888',   '#fff'],
    };
    const [bg, col] = kondisiMap[barang.kondisi] || ['#999','#fff'];

    let waPhone = (barang.seller_phone||'').replace(/\D/g,'');
    if (waPhone.startsWith('0')) waPhone = '62' + waPhone.slice(1);
    const waMsg = encodeURIComponent(`Halo kak, saya tertarik dengan *${barang.nama_barang}* seharga ${rupiah(barang.harga)}. Apakah masih tersedia?`);

    return (
        <div onClick={onClose} style={{position:'fixed',inset:0,background:'rgba(0,0,0,.78)',zIndex:9999,display:'flex',alignItems:'center',justifyContent:'center',padding:16,overflowY:'auto'}}>
            <div onClick={e=>e.stopPropagation()} style={{background:C.white,border:`5px solid ${C.black}`,boxShadow:`14px 14px 0 ${C.yellow}`,maxWidth:660,width:'100%',maxHeight:'92vh',overflow:'auto',position:'relative'}}>

                {/* Header modal */}
                <div style={{background:C.blue,color:C.white,borderBottom:`4px solid ${C.black}`,padding:'16px 20px',display:'flex',alignItems:'center',justifyContent:'space-between',position:'sticky',top:0,zIndex:2}}>
                    <div>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',letterSpacing:2,color:'#a8bcff',marginBottom:3}}>
                            {barang.icon} {barang.kategori} · 📍 {barang.kota}
                        </div>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.15rem',textTransform:'uppercase',letterSpacing:'-1px',lineHeight:1,color:C.yellow}}>
                            {barang.nama_barang}
                        </div>
                    </div>
                    <button onClick={onClose} style={{background:'rgba(255,255,255,.15)',color:C.white,border:`2px solid rgba(255,255,255,.4)`,width:38,height:38,fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',cursor:'pointer'}}>✕</button>
                </div>

                <div style={{padding:'20px 22px'}}>
                    {/* Gambar */}
                    <div style={{height:220,borderBottom:`3px solid ${C.black}`,marginBottom:20,overflow:'hidden',background:'#e8eeff'}}>
                        <img src={barang.img} alt={barang.nama_barang}
                            style={{width:'100%',height:'100%',objectFit:'cover',display:'block'}}
                            onError={e=>{e.target.style.display='none'; e.target.nextSibling.style.display='flex'}}
                        />
                        <div style={{display:'none',width:'100%',height:'100%',alignItems:'center',justifyContent:'center',flexDirection:'column',gap:6}}>
                            <span style={{fontSize:'5rem'}}>{barang.icon}</span>
                            <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',color:'#aaa',textTransform:'uppercase'}}>{barang.kategori}</span>
                        </div>
                    </div>

                    {/* Badge row */}
                    <div style={{display:'flex',gap:8,flexWrap:'wrap',marginBottom:16}}>
                        <span style={{background:bg,color:col,border:`2px solid ${C.black}`,padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase',boxShadow:`2px 2px 0 ${C.black}`}}>{barang.kondisi}</span>
                        {barang.rating && (
                            <span style={{background:C.yellow,color:C.black,border:`2px solid ${C.black}`,padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',boxShadow:`2px 2px 0 ${C.black}`}}>
                                ⭐ {barang.rating}/5 &nbsp;({barang.jumlah_review} ulasan)
                            </span>
                        )}
                        <span style={{background:barang.stok>0?C.green:'#FF4B4B',color:barang.stok>0?C.black:C.white,border:`2px solid ${C.black}`,padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase'}}>
                            {barang.stok>0 ? `✓ ${barang.stok} Tersedia` : '✗ Habis'}
                        </span>
                    </div>

                    {/* Tags */}
                    <div style={{display:'flex',gap:6,flexWrap:'wrap',marginBottom:16}}>
                        {(barang.tags||[]).map(t => (
                            <span key={t} style={{background:'#e8eeff',border:`2px solid ${C.black}`,padding:'3px 10px',fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',textTransform:'uppercase',boxShadow:`2px 2px 0 ${C.black}`,color:C.blue}}>
                                # {t}
                            </span>
                        ))}
                    </div>

                    {/* Deskripsi */}
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.9rem',lineHeight:1.75,color:'#333',marginBottom:18,padding:'14px 16px',background:'#f8f9ff',border:`2px solid #dde`}}>
                        {barang.deskripsi}
                    </div>

                    {/* Harga box */}
                    <div style={{border:`4px solid ${C.black}`,boxShadow:`5px 5px 0 ${C.black}`,background:C.blue,color:C.white,padding:'14px 20px',marginBottom:16,display:'flex',alignItems:'center',justifyContent:'space-between',flexWrap:'wrap',gap:8}}>
                        <div>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',letterSpacing:1,color:'#a8bcff',marginBottom:2}}>Harga</div>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'2rem',letterSpacing:'-2px',lineHeight:1,color:C.yellow}}>{rupiah(barang.harga)}</div>
                        </div>
                        <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.8rem',color:'#a8bcff'}}>
                            📍 {barang.kota}
                        </div>
                    </div>

                    {/* Penjual */}
                    {barang.seller_name && (
                        <div style={{border:`3px solid ${C.black}`,background:'#f4f4f0',padding:'12px 16px',marginBottom:16}}>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.62rem',textTransform:'uppercase',letterSpacing:1,marginBottom:8,color:'#666'}}>👤 Info Penjual</div>
                            <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:800,fontSize:'.95rem',marginBottom:3}}>{barang.seller_name}</div>
                            {barang.seller_phone && (
                                <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.82rem',color:'#555'}}>📞 {barang.seller_phone}</div>
                            )}
                        </div>
                    )}

                    {/* CTA buttons */}
                    <div style={{display:'flex',gap:8}}>
                        {waPhone && (
                            <a href={`https://wa.me/${waPhone}?text=${waMsg}`} target="_blank" rel="noopener noreferrer"
                                style={{flex:1,background:'#25D366',color:C.white,border:`3px solid ${C.black}`,padding:'13px 16px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',textAlign:'center',textDecoration:'none',boxShadow:`4px 4px 0 ${C.black}`,display:'block'}}>
                                💬 Chat WhatsApp
                            </a>
                        )}
                        <button onClick={onClose} style={{flex:1,background:'#eee',color:C.black,border:`3px solid ${C.black}`,padding:'13px 16px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',cursor:'pointer',boxShadow:`4px 4px 0 ${C.black}`}}>
                            ← Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

// ─── API JSON Preview ─────────────────────────────────
function ApiPreview({ filters, total }) {
    const [show, setShow] = useState(false);

    const sample = useMemo(() => MOCK_BARANG.filter(b => {
        if (filters.search && !b.nama_barang.toLowerCase().includes(filters.search.toLowerCase())) return false;
        if (filters.kategori && b.kategori !== filters.kategori) return false;
        if (filters.kota && b.kota !== filters.kota) return false;
        if (filters.kondisi && b.kondisi !== filters.kondisi) return false;
        if (filters.min_price && b.harga < +filters.min_price) return false;
        if (filters.max_price && b.harga > +filters.max_price) return false;
        return true;
    }).slice(0,2).map(b => ({id:b.id, nama_barang:b.nama_barang, harga:b.harga, kondisi:b.kondisi, kategori:b.kategori, kota:b.kota, rating:b.rating, stok:b.stok})), [filters]);

    const payload = {
        success: true,
        source: 'E-KOST Internal API v1.0 — Barang Kebutuhan Kos',
        total, data: sample, _note: '...dan seterusnya'
    };

    return (
        <div style={{border:`4px solid ${C.black}`,boxShadow:`6px 6px 0 ${C.black}`,background:'#0d1117',marginBottom:20}}>
            <div onClick={() => setShow(!show)}
                style={{display:'flex',justifyContent:'space-between',alignItems:'center',padding:'12px 18px',cursor:'pointer',borderBottom:show?'3px solid #30363d':'none',userSelect:'none'}}>
                <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.72rem',textTransform:'uppercase',letterSpacing:1,color:C.green}}>
                    📡 API Response Preview &nbsp;·&nbsp; <span style={{color:C.yellow}}>{total} barang</span>
                </span>
                <span style={{color:'#888',fontFamily:'Archivo Black,sans-serif',fontSize:'.68rem',textTransform:'uppercase'}}>{show?'▲ Tutup':'▼ Lihat JSON'}</span>
            </div>
            {show && (
                <pre style={{margin:0,padding:'16px',color:'#e6edf3',fontFamily:'monospace',fontSize:'.74rem',lineHeight:1.6,overflowX:'auto',maxHeight:300,overflowY:'auto'}}>
                    {JSON.stringify(payload, null, 2)}
                </pre>
            )}
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
        const t = setTimeout(() => setLoading(false), 700);
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
            <StatsBar total={filtered.length} />
            <FilterBar filters={filters} setFilters={setFilters} onSearch={setActiveFilters} />
            <ApiPreview filters={activeFilters} total={filtered.length} />

            {filtered.length === 0 ? (
                <div style={{textAlign:'center',padding:'70px 20px',border:`4px solid ${C.black}`,boxShadow:`6px 6px 0 ${C.black}`,background:C.white}}>
                    <div style={{fontSize:'3.5rem',marginBottom:12}}>🔍</div>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.1rem',textTransform:'uppercase',letterSpacing:1}}>Tidak Ada Barang Ditemukan</div>
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.9rem',color:'#888',marginTop:6}}>Coba ubah kata kunci atau hapus filter</div>
                </div>
            ) : (
                <>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase',letterSpacing:1,marginBottom:14,color:'#666',display:'flex',alignItems:'center',gap:8}}>
                        <span style={{background:C.blue,color:C.yellow,border:`2px solid ${C.black}`,padding:'2px 10px',fontSize:'.62rem'}}>
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
