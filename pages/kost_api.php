<?php include '../layouts/header.php'; ?>

<!-- Inject base_url ke JavaScript -->
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
    <!-- Page Header -->
    <div style="border:4px solid #000;box-shadow:8px 8px 0 #000;background:#001ee1;color:#FFD600;padding:22px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-family:'Archivo Black',sans-serif;font-size:.6rem;text-transform:uppercase;letter-spacing:2px;opacity:.7;margin-bottom:4px;">⚛️ React + Axios · E-KOST Internal API</div>
            <h1 style="font-family:'Archivo Black',sans-serif;font-size:clamp(1.4rem,4vw,2.4rem);text-transform:uppercase;letter-spacing:-2px;line-height:1;margin:0;">Katalog <span style="color:#fff;">Pencarian Kost</span></h1>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="background:#FFD600;color:#000;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">⚛️ useState</span>
            <span style="background:#FF3CAC;color:#fff;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">🔄 useEffect</span>
            <span style="background:#00FF94;color:#000;border:3px solid #000;padding:6px 14px;font-family:'Archivo Black',sans-serif;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;box-shadow:3px 3px 0 #000;">🌐 Axios API</span>
        </div>
    </div>
    <div id="react-root"></div>
</div>

<script type="text/babel">
const { useState, useEffect, useCallback } = React;

// ─── API base ────────────────────────────────────────
const API = window.BASE_URL + 'modules/api/kost.php';

// ─── Format Rupiah ───────────────────────────────────
function rupiah(n) {
    if (!n) return 'Hubungi Pemilik';
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

// ─── Stars ───────────────────────────────────────────
function Stars({ rating }) {
    const r = Math.round(rating || 0);
    return <span style={{color:'#FFD600',letterSpacing:1}}>{'★'.repeat(r)}{'☆'.repeat(5-r)}</span>;
}

// ─── Loading ─────────────────────────────────────────
function Loading() {
    return (
        <div style={{display:'flex',flexDirection:'column',alignItems:'center',padding:'80px 0',gap:16}}>
            <div style={{width:56,height:56,border:'5px solid #000',borderTop:'5px solid #FFD600',borderRadius:'50%',animation:'spin .8s linear infinite',boxShadow:'4px 4px 0 #000'}}/>
            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',textTransform:'uppercase',letterSpacing:2}}>Memuat Data Kost...</div>
            <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.82rem',color:'#888'}}>Mengambil data via Axios dari E-KOST Internal API</div>
            <style>{`@keyframes spin{to{transform:rotate(360deg)}}`}</style>
        </div>
    );
}

// ─── Stats Bar ───────────────────────────────────────
function StatsBar({ stats, total }) {
    if (!stats) return null;
    const items = [
        { label:'Total Kost',    value: stats.total_kost,   bg:'#001ee1', color:'#FFD600' },
        { label:'Kamar Tersedia',value: stats.available,    bg:'#00FF94', color:'#000'    },
        { label:'Kota',          value: stats.cities,       bg:'#FF5C00', color:'#fff'    },
        { label:'Harga Mulai',   value: 'Rp '+Number(stats.min_price).toLocaleString('id-ID'), bg:'#FFD600', color:'#000' },
        { label:'Hasil Filter',  value: total,              bg:'#FF3CAC', color:'#fff'    },
    ];
    return (
        <div style={{display:'flex',gap:10,marginBottom:20,flexWrap:'wrap'}}>
            {items.map(s => (
                <div key={s.label} style={{background:s.bg,color:s.color,border:'3px solid #000',boxShadow:'4px 4px 0 #000',padding:'10px 16px',flex:'1 1 80px',minWidth:80}}>
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.62rem',textTransform:'uppercase',letterSpacing:1,opacity:.8}}>{s.label}</div>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.4rem',letterSpacing:'-1px',lineHeight:1.1}}>{s.value}</div>
                </div>
            ))}
        </div>
    );
}

// ─── Filter Bar ──────────────────────────────────────
function FilterBar({ filters, setFilters, cities, onSearch }) {
    const [local, setLocal] = useState(filters);

    function handleSubmit(e) {
        e.preventDefault();
        setFilters(local);
        onSearch(local);
    }

    function handleReset() {
        const empty = { city:'', type:'', min_price:'', max_price:'', fasilitas:'' };
        setLocal(empty);
        setFilters(empty);
        onSearch(empty);
    }

    const inputStyle = { width:'100%', border:'3px solid #000', padding:'9px 12px', fontFamily:'Space Grotesk,sans-serif', fontWeight:600, fontSize:'.88rem', outline:'none', background:'#fff' };
    const labelStyle = { fontFamily:'Archivo Black,sans-serif', fontSize:'.65rem', textTransform:'uppercase', letterSpacing:'1px', display:'block', marginBottom:5 };

    return (
        <form onSubmit={handleSubmit} style={{border:'4px solid #000',boxShadow:'6px 6px 0 #000',background:'#fff',padding:'20px 22px',marginBottom:20}}>
            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.75rem',textTransform:'uppercase',letterSpacing:1,marginBottom:14,paddingBottom:10,borderBottom:'2px solid #000',color:'#001ee1'}}>
                🔍 Filter Pencarian Kost
            </div>
            <div style={{display:'grid',gridTemplateColumns:'repeat(auto-fit,minmax(150px,1fr))',gap:12,marginBottom:14}}>

                {/* Kota */}
                <div>
                    <label style={labelStyle}>📍 Kota</label>
                    <select value={local.city} onChange={e=>setLocal({...local,city:e.target.value})} style={{...inputStyle,cursor:'pointer',appearance:'none',background:'#fff'}}>
                        <option value="">Semua Kota</option>
                        {cities.map(c => <option key={c.city} value={c.city}>{c.city} ({c.total})</option>)}
                    </select>
                </div>

                {/* Tipe */}
                <div>
                    <label style={labelStyle}>🏠 Tipe Kost</label>
                    <select value={local.type} onChange={e=>setLocal({...local,type:e.target.value})} style={{...inputStyle,cursor:'pointer',appearance:'none',background:'#fff'}}>
                        <option value="">Semua Tipe</option>
                        <option value="Putra">🧔 Putra</option>
                        <option value="Putri">👩 Putri</option>
                        <option value="Campur">👥 Campur</option>
                    </select>
                </div>

                {/* Harga Min */}
                <div>
                    <label style={labelStyle}>💰 Harga Min (Rp)</label>
                    <input type="number" placeholder="Contoh: 500000" value={local.min_price}
                        onChange={e=>setLocal({...local,min_price:e.target.value})} style={inputStyle} />
                </div>

                {/* Harga Max */}
                <div>
                    <label style={labelStyle}>💰 Harga Max (Rp)</label>
                    <input type="number" placeholder="Contoh: 2000000" value={local.max_price}
                        onChange={e=>setLocal({...local,max_price:e.target.value})} style={inputStyle} />
                </div>

                {/* Fasilitas */}
                <div>
                    <label style={labelStyle}>✨ Fasilitas</label>
                    <input type="text" placeholder="WiFi, AC, Parkir..." value={local.fasilitas}
                        onChange={e=>setLocal({...local,fasilitas:e.target.value})} style={inputStyle} />
                </div>
            </div>

            {/* Quick Filters */}
            <div style={{display:'flex',gap:6,flexWrap:'wrap',marginBottom:14}}>
                <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',textTransform:'uppercase',letterSpacing:1,alignSelf:'center',marginRight:4}}>Cepat:</span>
                {[
                    {label:'≤ 1 Juta', max:'1000000'},
                    {label:'≤ 1.5 Juta', max:'1500000'},
                    {label:'≤ 2 Juta', max:'2000000'},
                    {label:'AC', fas:'AC'},
                    {label:'WiFi', fas:'WiFi'},
                    {label:'KM Dalam', fas:'Kamar Mandi Dalam'},
                ].map(q => (
                    <button type="button" key={q.label}
                        onClick={() => {
                            const n = {...local, ...(q.max?{max_price:q.max}:{}), ...(q.fas?{fasilitas:q.fas}:{})};
                            setLocal(n); setFilters(n); onSearch(n);
                        }}
                        style={{background:'#f4f4f0',color:'#000',border:'2px solid #000',padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',textTransform:'uppercase',cursor:'pointer',boxShadow:'2px 2px 0 #000'}}>
                        {q.label}
                    </button>
                ))}
            </div>

            <div style={{display:'flex',gap:8}}>
                <button type="submit" style={{background:'#001ee1',color:'#FFD600',border:'3px solid #000',padding:'10px 24px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',cursor:'pointer',boxShadow:'4px 4px 0 #000',flex:1}}>
                    🔍 Cari Kost
                </button>
                <button type="button" onClick={handleReset} style={{background:'#eee',color:'#000',border:'3px solid #000',padding:'10px 18px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',cursor:'pointer',boxShadow:'4px 4px 0 #000'}}>
                    Reset
                </button>
            </div>
        </form>
    );
}

// ─── Kost Card ───────────────────────────────────────
function KostCard({ kost, onSelect }) {
    const [hovered, setHovered] = useState(false);

    const typeColor = { Putra:['#001ee1','#FFD600'], Putri:['#FF3CAC','#fff'], Campur:['#FF5C00','#fff'] };
    const [bg, col] = typeColor[kost.type] || ['#000','#fff'];

    const fasList = kost.facilities_list?.slice(0,4) || [];

    return (
        <div onClick={() => onSelect(kost)}
            onMouseEnter={()=>setHovered(true)} onMouseLeave={()=>setHovered(false)}
            style={{border:'4px solid #000',boxShadow:hovered?'10px 10px 0 #000':'6px 6px 0 #000',background:'#fff',cursor:'pointer',transform:hovered?'translate(-3px,-3px)':'none',transition:'all .12s',display:'flex',flexDirection:'column',height:'100%'}}>

            {/* Gambar */}
            <div style={{position:'relative',borderBottom:'3px solid #000',height:180,background:'#f0f0f0',overflow:'hidden',display:'flex',alignItems:'center',justifyContent:'center'}}>
                {kost.image_url ? (
                    <img src={kost.image_url} alt={kost.name} style={{width:'100%',height:'100%',objectFit:'cover'}}
                         onError={e=>{e.target.style.display='none'; e.target.nextSibling.style.display='flex'}} />
                ) : null}
                <div style={{display: kost.image_url ? 'none' : 'flex', flexDirection:'column', alignItems:'center', justifyContent:'center', width:'100%', height:'100%', background:'#eef1ff'}}>
                    <span style={{fontSize:'3rem'}}>🏠</span>
                    <span style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',color:'#aaa',textTransform:'uppercase',marginTop:4}}>Foto Belum Tersedia</span>
                </div>

                {/* Type badge */}
                <span style={{position:'absolute',top:8,left:8,background:bg,color:col,border:'2px solid #000',padding:'3px 10px',fontFamily:'Archivo Black,sans-serif',fontSize:'.62rem',textTransform:'uppercase',boxShadow:'2px 2px 0 #000'}}>
                    {kost.type}
                </span>

                {/* Available badge */}
                {kost.rooms_available > 0 ? (
                    <span style={{position:'absolute',top:8,right:8,background:'#00FF94',color:'#000',border:'2px solid #000',padding:'3px 8px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',boxShadow:'2px 2px 0 #000'}}>
                        {kost.rooms_available} Kamar
                    </span>
                ) : (
                    <span style={{position:'absolute',top:8,right:8,background:'#FF4B4B',color:'#fff',border:'2px solid #000',padding:'3px 8px',fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase'}}>
                        Penuh
                    </span>
                )}
            </div>

            {/* Body */}
            <div style={{padding:'14px 16px',flex:1,display:'flex',flexDirection:'column',gap:6}}>
                <h3 style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.95rem',textTransform:'uppercase',letterSpacing:'-0.5px',margin:0,lineHeight:1.2,display:'-webkit-box',WebkitLineClamp:2,WebkitBoxOrient:'vertical',overflow:'hidden'}}>
                    {kost.name}
                </h3>
                <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.78rem',color:'#666'}}>
                    📍 {kost.address?.length > 40 ? kost.address.slice(0,40)+'...' : kost.address}
                </div>
                <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.75rem',color:'#001ee1'}}>
                    🏙️ {kost.city}
                </div>

                {/* Rating */}
                {kost.avg_rating && (
                    <div style={{display:'flex',alignItems:'center',gap:6}}>
                        <Stars rating={kost.avg_rating}/>
                        <span style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.72rem',color:'#888'}}>{kost.avg_rating} ({kost.review_count} ulasan)</span>
                    </div>
                )}

                {/* Fasilitas mini */}
                <div style={{display:'flex',flexWrap:'wrap',gap:4,marginTop:2}}>
                    {fasList.map(f => (
                        <span key={f} style={{background:'#eef1ff',color:'#001ee1',border:'1.5px solid #001ee1',padding:'2px 8px',fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.65rem'}}>
                            {f}
                        </span>
                    ))}
                    {kost.facilities_list?.length > 4 && (
                        <span style={{background:'#f4f4f0',color:'#888',border:'1.5px solid #ccc',padding:'2px 8px',fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.65rem'}}>
                            +{kost.facilities_list.length-4} lagi
                        </span>
                    )}
                </div>

                {/* Harga */}
                <div style={{marginTop:'auto',paddingTop:8,borderTop:'2px solid #eee'}}>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.15rem',letterSpacing:'-1px',color:'#001ee1'}}>
                        {rupiah(kost.price_min)}
                        {kost.price_max && kost.price_max !== kost.price_min && <span style={{fontSize:'.8rem',color:'#666'}}> – {rupiah(kost.price_max)}</span>}
                    </div>
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.72rem',color:'#888'}}>/bulan</div>
                </div>
            </div>

            {/* CTA */}
            <div style={{padding:'0 12px 12px'}}>
                <div style={{background:'#001ee1',color:'#FFD600',border:'3px solid #000',padding:'8px',fontFamily:'Archivo Black,sans-serif',fontSize:'.72rem',textTransform:'uppercase',textAlign:'center',boxShadow:'3px 3px 0 #000',letterSpacing:.5}}>
                    Lihat Detail →
                </div>
            </div>
        </div>
    );
}

// ─── Modal Detail Kost ───────────────────────────────
function KostModal({ kost, onClose }) {
    if (!kost) return null;
    const typeColor = { Putra:['#001ee1','#FFD600'], Putri:['#FF3CAC','#fff'], Campur:['#FF5C00','#fff'] };
    const [bg,col] = typeColor[kost.type]||['#000','#fff'];

    // Format WA
    let waPhone = (kost.owner_phone||'').replace(/\D/g,'');
    if (waPhone.startsWith('0')) waPhone = '62'+waPhone.slice(1);
    const waMsg = encodeURIComponent(`Halo, saya tertarik dengan kost *${kost.name}*. Apakah masih tersedia?`);

    return (
        <div onClick={onClose} style={{position:'fixed',inset:0,background:'rgba(0,0,0,.75)',zIndex:9999,display:'flex',alignItems:'center',justifyContent:'center',padding:16,overflowY:'auto'}}>
            <div onClick={e=>e.stopPropagation()} style={{background:'#fff',border:'5px solid #000',boxShadow:'14px 14px 0 #FFD600',maxWidth:640,width:'100%',maxHeight:'90vh',overflow:'auto',position:'relative'}}>

                {/* Header modal */}
                <div style={{background:bg,color:col,borderBottom:'4px solid #000',padding:'16px 20px',display:'flex',alignItems:'center',justifyContent:'space-between',position:'sticky',top:0,zIndex:2}}>
                    <div>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',letterSpacing:2,opacity:.8,marginBottom:3}}>Detail Kost · {kost.city}</div>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.1rem',textTransform:'uppercase',letterSpacing:'-1px',lineHeight:1}}>{kost.name}</div>
                    </div>
                    <button onClick={onClose} style={{background:'rgba(0,0,0,.3)',color:'#fff',border:'2px solid rgba(255,255,255,.5)',width:36,height:36,fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',cursor:'pointer'}}>✕</button>
                </div>

                <div style={{padding:'20px 22px'}}>
                    {/* Image */}
                    <div style={{height:200,background:'#eef1ff',borderBottom:'3px solid #000',marginBottom:20,display:'flex',alignItems:'center',justifyContent:'center',overflow:'hidden'}}>
                        {kost.image_url
                            ? <img src={kost.image_url} alt={kost.name} style={{width:'100%',height:'100%',objectFit:'cover'}} onError={e=>{e.target.style.display='none'}}/>
                            : <span style={{fontSize:'4rem'}}>🏠</span>
                        }
                    </div>

                    {/* Tags row */}
                    <div style={{display:'flex',gap:8,flexWrap:'wrap',marginBottom:16}}>
                        <span style={{background:bg,color:col,border:'2px solid #000',padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase',boxShadow:'2px 2px 0 #000'}}>{kost.type}</span>
                        {kost.avg_rating && <span style={{background:'#FFD600',color:'#000',border:'2px solid #000',padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',boxShadow:'2px 2px 0 #000'}}>⭐ {kost.avg_rating}/5 ({kost.review_count})</span>}
                        <span style={{background:kost.rooms_available>0?'#00FF94':'#FF4B4B',color:kost.rooms_available>0?'#000':'#fff',border:'2px solid #000',padding:'4px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase'}}>{kost.rooms_available>0?`${kost.rooms_available} kamar tersedia`:'Penuh'}</span>
                    </div>

                    {/* Address */}
                    <div style={{border:'3px solid #000',background:'#fffde7',padding:'12px 16px',marginBottom:16,fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.88rem'}}>
                        📍 {kost.address} — <strong>{kost.city}</strong>
                    </div>

                    {/* Deskripsi */}
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.88rem',lineHeight:1.7,color:'#444',marginBottom:16}}>
                        {kost.description}
                    </div>

                    {/* Harga */}
                    <div style={{border:'4px solid #000',boxShadow:'4px 4px 0 #000',background:'#001ee1',color:'#FFD600',padding:'14px 18px',marginBottom:16,display:'flex',justifyContent:'space-between',alignItems:'center',flexWrap:'wrap',gap:8}}>
                        <div>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',textTransform:'uppercase',letterSpacing:1,opacity:.7,marginBottom:4}}>Harga Mulai</div>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.8rem',letterSpacing:'-2px',lineHeight:1}}>{rupiah(kost.price_min)}<small style={{fontSize:'.8rem',marginLeft:4,opacity:.8}}>/bln</small></div>
                        </div>
                        {kost.price_max && kost.price_max!==kost.price_min && (
                            <div style={{textAlign:'right'}}>
                                <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.6rem',opacity:.7,marginBottom:4}}>S/D</div>
                                <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.2rem'}}>{rupiah(kost.price_max)}/bln</div>
                            </div>
                        )}
                    </div>

                    {/* Fasilitas */}
                    {kost.facilities_list?.length > 0 && (
                        <div style={{marginBottom:16}}>
                            <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase',letterSpacing:1,marginBottom:8}}>✨ Fasilitas</div>
                            <div style={{display:'flex',flexWrap:'wrap',gap:6}}>
                                {kost.facilities_list.map(f=>(
                                    <span key={f} style={{background:'#eef1ff',color:'#001ee1',border:'2px solid #001ee1',padding:'4px 12px',fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.78rem'}}>{f}</span>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Pemilik */}
                    <div style={{border:'3px solid #000',background:'#f4f4f0',padding:'12px 16px',marginBottom:16}}>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',textTransform:'uppercase',letterSpacing:1,marginBottom:6}}>👤 Pemilik Kost</div>
                        <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.9rem'}}>{kost.owner_name}</div>
                        {kost.owner_phone && <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.82rem',color:'#666'}}>{kost.owner_phone}</div>}
                    </div>

                    {/* CTA Buttons */}
                    <div style={{display:'flex',gap:8,flexWrap:'wrap'}}>
                        <a href={`${window.BASE_URL}pages/kost_detail.php?id=${kost.id}`}
                           style={{flex:1,background:'#001ee1',color:'#FFD600',border:'3px solid #000',padding:'12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',textAlign:'center',textDecoration:'none',boxShadow:'4px 4px 0 #000',display:'block'}}>
                            📋 Lihat Lengkap
                        </a>
                        {kost.owner_phone && (
                            <a href={`https://wa.me/${waPhone}?text=${waMsg}`} target="_blank"
                               style={{flex:1,background:'#25D366',color:'#fff',border:'3px solid #000',padding:'12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',textAlign:'center',textDecoration:'none',boxShadow:'4px 4px 0 #000',display:'block'}}>
                                <i className="bi bi-whatsapp"></i> Chat WA
                            </a>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

// ─── MAIN APP ─────────────────────────────────────────
function KostApiApp() {
    // ══ STATE ══════════════════════════════════════════
    const [kosts,    setKosts]    = useState([]);          // data kost dari API
    const [stats,    setStats]    = useState(null);        // statistik global
    const [cities,   setCities]   = useState([]);          // daftar kota
    const [loading,  setLoading]  = useState(true);        // loading state
    const [error,    setError]    = useState(null);        // error state
    const [selected, setSelected] = useState(null);        // modal detail
    const [total,    setTotal]    = useState(0);           // total hasil
    const [page,     setPage]     = useState(1);           // halaman aktif
    const [totalPages,setTotalPages]=useState(1);
    const [filters,  setFilters]  = useState({ city:'', type:'', min_price:'', max_price:'', fasilitas:'' });
    const [sortView, setSortView] = useState('grid');      // grid / list

    // ══ EFFECT — Fetch stats & cities saat mount ══════
    useEffect(() => {
        fetchStats();
        fetchCities();
    }, []);

    // ══ EFFECT — Fetch kost saat filter/page berubah ══
    useEffect(() => {
        fetchKosts(filters, page);
    }, [page]);

    // ══ API CALLS ──────────────────────────────────────
    async function fetchStats() {
        try {
            const res = await axios.get(`${API}?action=stats`);
            if (res.data.success) setStats(res.data.data);
        } catch(e) {}
    }

    async function fetchCities() {
        try {
            const res = await axios.get(`${API}?action=cities`);
            if (res.data.success) setCities(res.data.data);
        } catch(e) {}
    }

    async function fetchKosts(f = filters, pg = 1) {
        setLoading(true);
        setError(null);
        try {
            const params = {
                action: 'list', page: pg, limit: 12,
                ...(f.city      && { city:      f.city }),
                ...(f.type      && { type:      f.type }),
                ...(f.min_price && { min_price: f.min_price }),
                ...(f.max_price && { max_price: f.max_price }),
                ...(f.fasilitas && { fasilitas: f.fasilitas }),
            };
            // ── AXIOS GET dari E-KOST Internal API ──
            const res = await axios.get(API, { params });
            if (res.data.success) {
                setKosts(res.data.data);
                setTotal(res.data.total);
                setTotalPages(res.data.pages);
            } else {
                setError('Gagal memuat data dari API.');
            }
        } catch(e) {
            setError('Koneksi ke API gagal: ' + e.message);
        } finally {
            setLoading(false);
        }
    }

    function handleSearch(f) {
        setPage(1);
        fetchKosts(f, 1);
    }

    function changePage(p) {
        setPage(p);
        window.scrollTo({ top: 0, behavior:'smooth' });
    }

    // ══ RENDER ═════════════════════════════════════════
    return (
        <div>
            {/* Stats */}
            <StatsBar stats={stats} total={total} />

            {/* Filter */}
            <FilterBar filters={filters} setFilters={setFilters} cities={cities} onSearch={handleSearch} />

            {/* Toolbar */}
            <div style={{display:'flex',justifyContent:'space-between',alignItems:'center',marginBottom:16,flexWrap:'wrap',gap:8}}>
                <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.85rem',color:'#444'}}>
                    {loading ? 'Memuat...' : <span><strong style={{color:'#001ee1',fontFamily:'Archivo Black,sans-serif'}}>{total}</strong> kost ditemukan</span>}
                </div>
                <div style={{display:'flex',gap:6}}>
                    {['grid','list'].map(v=>(
                        <button key={v} onClick={()=>setSortView(v)}
                            style={{background:sortView===v?'#001ee1':'#fff',color:sortView===v?'#FFD600':'#000',border:'2px solid #000',padding:'6px 12px',fontFamily:'Archivo Black,sans-serif',fontSize:'.7rem',textTransform:'uppercase',cursor:'pointer'}}>
                            {v==='grid'?'⊞ Grid':'☰ List'}
                        </button>
                    ))}
                </div>
            </div>

            {/* ── CONDITIONAL RENDERING ── */}
            {loading && <Loading />}

            {!loading && error && (
                <div style={{border:'4px solid #000',boxShadow:'6px 6px 0 #000',background:'#FF4B4B',color:'#fff',padding:'24px',marginBottom:24,display:'flex',gap:16,alignItems:'center',flexWrap:'wrap'}}>
                    <span style={{fontSize:'2rem'}}>⚠️</span>
                    <div style={{flex:1}}>
                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1rem',textTransform:'uppercase',marginBottom:4}}>Gagal Memuat Data</div>
                        <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.88rem'}}>{error}</div>
                    </div>
                    <button onClick={()=>fetchKosts(filters,page)} style={{background:'#FFD600',color:'#000',border:'3px solid #000',padding:'10px 20px',fontFamily:'Archivo Black,sans-serif',fontSize:'.8rem',textTransform:'uppercase',cursor:'pointer',boxShadow:'3px 3px 0 #000'}}>Coba Lagi</button>
                </div>
            )}

            {!loading && !error && kosts.length === 0 && (
                <div style={{border:'4px solid #000',boxShadow:'6px 6px 0 #000',background:'#fff',padding:'60px 24px',textAlign:'center'}}>
                    <div style={{fontSize:'3.5rem',marginBottom:12}}>🏚️</div>
                    <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.4rem',textTransform:'uppercase',letterSpacing:'-1px',marginBottom:8}}>Kost Tidak Ditemukan</div>
                    <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,color:'#888',marginBottom:20}}>Coba ubah filter pencarian atau hapus beberapa kriteria</div>
                    <button onClick={()=>handleSearch({city:'',type:'',min_price:'',max_price:'',fasilitas:''})}
                        style={{background:'#FFD600',color:'#000',border:'3px solid #000',padding:'12px 28px',fontFamily:'Archivo Black,sans-serif',fontSize:'.82rem',textTransform:'uppercase',cursor:'pointer',boxShadow:'4px 4px 0 #000'}}>
                        Tampilkan Semua Kost
                    </button>
                </div>
            )}

            {!loading && !error && kosts.length > 0 && (
                <>
                    {/* Grid/List view */}
                    {sortView === 'grid' ? (
                        <div style={{display:'grid',gridTemplateColumns:'repeat(auto-fill,minmax(280px,1fr))',gap:20,marginBottom:24}}>
                            {kosts.map(k => <KostCard key={k.id} kost={k} onSelect={setSelected}/>)}
                        </div>
                    ) : (
                        <div style={{display:'flex',flexDirection:'column',gap:12,marginBottom:24}}>
                            {kosts.map(k => (
                                <div key={k.id} onClick={()=>setSelected(k)} style={{border:'4px solid #000',boxShadow:'5px 5px 0 #000',background:'#fff',display:'flex',gap:0,cursor:'pointer',transition:'all .12s'}}
                                    onMouseEnter={e=>{e.currentTarget.style.transform='translate(-2px,-2px)';e.currentTarget.style.boxShadow='7px 7px 0 #000'}}
                                    onMouseLeave={e=>{e.currentTarget.style.transform='none';e.currentTarget.style.boxShadow='5px 5px 0 #000'}}>
                                    <div style={{width:120,flexShrink:0,background:'#eef1ff',borderRight:'3px solid #000',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'2.5rem'}}>
                                        {k.image_url ? <img src={k.image_url} alt={k.name} style={{width:'100%',height:'100%',objectFit:'cover'}} onError={e=>{e.target.style.display='none'}}/> : '🏠'}
                                    </div>
                                    <div style={{padding:'14px 16px',flex:1}}>
                                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.92rem',textTransform:'uppercase',letterSpacing:'-0.5px',marginBottom:4}}>{k.name}</div>
                                        <div style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.78rem',color:'#666',marginBottom:6}}>📍 {k.city} · {k.type}</div>
                                        <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'1.1rem',color:'#001ee1'}}>{rupiah(k.price_min)}<small style={{fontFamily:'Space Grotesk,sans-serif',fontWeight:600,fontSize:'.7rem',color:'#888'}}>/bln</small></div>
                                    </div>
                                    <div style={{display:'flex',alignItems:'center',padding:'0 16px',borderLeft:'3px solid #000',background:'#001ee1',color:'#FFD600',fontFamily:'Archivo Black,sans-serif',fontSize:'1.2rem'}}>→</div>
                                </div>
                            ))}
                        </div>
                    )}

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div style={{display:'flex',justifyContent:'center',gap:6,marginBottom:32,flexWrap:'wrap'}}>
                            <button onClick={()=>changePage(Math.max(1,page-1))} disabled={page===1}
                                style={{background:page===1?'#eee':'#000',color:page===1?'#aaa':'#FFD600',border:'3px solid #000',padding:'8px 16px',fontFamily:'Archivo Black,sans-serif',fontSize:'.78rem',textTransform:'uppercase',cursor:page===1?'not-allowed':'pointer',boxShadow:page===1?'none':'3px 3px 0 #FFD600'}}>
                                ← Prev
                            </button>
                            {Array.from({length:totalPages},(_,i)=>i+1).map(n=>(
                                <button key={n} onClick={()=>changePage(n)}
                                    style={{background:n===page?'#001ee1':'#fff',color:n===page?'#FFD600':'#000',border:'3px solid #000',padding:'8px 14px',fontFamily:'Archivo Black,sans-serif',fontSize:'.78rem',cursor:'pointer',boxShadow:n===page?'3px 3px 0 #FFD600':'2px 2px 0 #000'}}>
                                    {n}
                                </button>
                            ))}
                            <button onClick={()=>changePage(Math.min(totalPages,page+1))} disabled={page===totalPages}
                                style={{background:page===totalPages?'#eee':'#000',color:page===totalPages?'#aaa':'#FFD600',border:'3px solid #000',padding:'8px 16px',fontFamily:'Archivo Black,sans-serif',fontSize:'.78rem',textTransform:'uppercase',cursor:page===totalPages?'not-allowed':'pointer',boxShadow:page===totalPages?'none':'3px 3px 0 #FFD600'}}>
                                Next →
                            </button>
                        </div>
                    )}
                </>
            )}

            {/* Tech info */}
            <div style={{border:'4px solid #000',boxShadow:'6px 6px 0 #000',background:'#000',color:'#FFD600',padding:'18px 22px',marginBottom:32}}>
                <div style={{fontFamily:'Archivo Black,sans-serif',fontSize:'.65rem',textTransform:'uppercase',letterSpacing:'2px',opacity:.7,marginBottom:8}}>✦ Implementasi UTS — Tech Stack</div>
                <div style={{display:'flex',gap:8,flexWrap:'wrap'}}>
                    {['⚛️ React 18 — useState (8 state)','⚛️ React 18 — useEffect (2 hooks)','🎨 Conditional Rendering (loading/error/empty/data)','🌐 Axios GET — E-KOST Internal API','📦 10+ Data Kost Real dari MySQL','🔍 Filter: Kota, Tipe, Harga, Fasilitas','⚡ Quick Filter Shortcut','📄 Pagination & Grid/List View','💬 WhatsApp Direct Chat','🗂️ Modal Detail Kost'].map(t=>(
                        <span key={t} style={{background:'rgba(255,214,0,.1)',border:'2px solid rgba(255,214,0,.3)',color:'#FFD600',padding:'4px 10px',fontFamily:'Space Grotesk,sans-serif',fontWeight:700,fontSize:'.72rem'}}>{t}</span>
                    ))}
                </div>
            </div>

            {/* Modal */}
            {selected && <KostModal kost={selected} onClose={()=>setSelected(null)}/>}
        </div>
    );
}

const root = ReactDOM.createRoot(document.getElementById('react-root'));
root.render(<KostApiApp />);
</script>

<?php include '../layouts/footer.php'; ?>
