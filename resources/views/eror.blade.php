<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Error â€” MTs Syafiiyah</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse-glow {
      0%, 100% { opacity: 0.5; transform: scale(1); }
      50%       { opacity: 1;   transform: scale(1.08); }
    }
    @keyframes bounce-qmark {
      0%, 100% { transform: translateY(0) rotate(-8deg); }
      50%       { transform: translateY(-10px) rotate(8deg); }
    }
    @keyframes spin-slow  { to { transform: rotate(360deg); } }
    @keyframes blink-dot  { 0%,100%{opacity:1} 50%{opacity:.2} }

    .anim-fadeup { animation: fadeInUp .7s ease both; }
    .delay-1 { animation-delay: .1s; }
    .delay-2 { animation-delay: .25s; }
    .delay-3 { animation-delay: .4s; }
    .delay-4 { animation-delay: .55s; }
    .delay-5 { animation-delay: .7s; }
    .delay-6 { animation-delay: .85s; }

    .glow-red    { text-shadow: 0 0 60px rgba(220,80,80,.8), 0 2px 0 rgba(0,0,0,.5); }
    .glow-yellow { text-shadow: 0 0 60px rgba(240,180,40,.8), 0 2px 0 rgba(0,0,0,.5); }
    .glow-blue   { text-shadow: 0 0 60px rgba(100,160,255,.8),0 2px 0 rgba(0,0,0,.5); }
    .glow-gray   { text-shadow: 0 0 40px rgba(160,160,180,.5),0 2px 0 rgba(0,0,0,.5); }

    #canvas3d { display: block; width: 100%; height: 100%; }

    .tab-btn { transition: all .2s; }
    .tab-btn.active-404 { background: rgba(220,80,80,.25); border-color: rgba(220,80,80,.7); color: #f08080; }
    .tab-btn.active-500 { background: rgba(240,180,40,.2);  border-color: rgba(240,180,40,.6); color: #f0c050; }
    .tab-btn.active-403 { background: rgba(100,160,255,.2); border-color: rgba(100,160,255,.6);color: #80b8ff; }
    .tab-btn.active-offline { background: rgba(160,160,180,.15); border-color: rgba(160,160,180,.5); color: #b0b0c8; }

    .btn-primary { transition: opacity .2s, transform .1s; }
    .btn-primary:hover  { opacity: .85; }
    .btn-primary:active { transform: scale(.97); }
    .btn-secondary { transition: background .2s, transform .1s; }
    .btn-secondary:hover  { background: rgba(255,255,255,.12); }
    .btn-secondary:active { transform: scale(.97); }

    .status-dot { animation: blink-dot 1.4s ease-in-out infinite; }
    .loading-dot { animation: blink-dot 1.4s ease-in-out infinite; }
    .loading-dot:nth-child(2) { animation-delay: .2s; }
    .loading-dot:nth-child(3) { animation-delay: .4s; }
  </style>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            dark: { DEFAULT: '#070715', 800: '#0e0e28', 700: '#141430' }
          }
        }
      }
    }
  </script>
</head>

<body class="bg-[#070715] text-white min-h-screen overflow-x-hidden">

  <!-- ==================== CANVAS 3D ==================== -->
  <div class="absolute inset-0 z-0">
    <canvas id="canvas3d"></canvas>
  </div>

  <!-- ==================== OVERLAY UI ==================== -->
  <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-12 text-center">

    <!-- School Badge -->
    <div class="anim-fadeup delay-1 mb-1">
      <span class="text-[10px] tracking-[3px] uppercase text-slate-400 font-medium">MTs Syafiiyah</span>
    </div>
    <div class="anim-fadeup delay-1 flex items-center gap-1.5 text-slate-400 text-[13px] mb-8">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-4-4h8"/>
      </svg>
      Aplikasi Pembelajaran Digital
    </div>

    <!-- Error Type Tabs -->
    <div class="anim-fadeup delay-2 flex flex-wrap gap-2 justify-center mb-10">
      <button class="tab-btn active-404 text-[11px] px-3.5 py-1 rounded-full border border-transparent" data-e="404" onclick="switchError('404',this)">404 Tidak Ditemukan</button>
      <button class="tab-btn text-[11px] px-3.5 py-1 rounded-full border border-slate-600 text-slate-400" data-e="500" onclick="switchError('500',this)">500 Server Error</button>
      <button class="tab-btn text-[11px] px-3.5 py-1 rounded-full border border-slate-600 text-slate-400" data-e="403" onclick="switchError('403',this)">403 Akses Ditolak</button>
      <button class="tab-btn text-[11px] px-3.5 py-1 rounded-full border border-slate-600 text-slate-400" data-e="offline" onclick="switchError('offline',this)">Offline</button>
    </div>

    <!-- Error Code -->
    <div id="errCode"
         class="anim-fadeup delay-3 glow-red font-bold text-[90px] sm:text-[110px] leading-none tracking-tight text-white transition-all duration-500 select-none">
      404
    </div>

    <!-- Divider line -->
    <div class="anim-fadeup delay-3 w-10 h-px bg-white/10 my-5 mx-auto"></div>

    <!-- Title & Description -->
    <h1 id="errTitle" class="anim-fadeup delay-4 text-xl sm:text-2xl font-semibold text-white/90 mb-3 transition-all duration-300">
      Halaman Tidak Ditemukan
    </h1>
    <p id="errDesc" class="anim-fadeup delay-4 text-sm text-slate-400 max-w-sm leading-relaxed transition-all duration-300">
      Maaf, halaman yang kamu cari tidak tersedia di sistem pembelajaran. Mungkin telah dipindahkan atau dihapus.
    </p>

    <!-- Action Buttons -->
    <div class="anim-fadeup delay-5 flex flex-wrap gap-3 justify-center mt-8">
      <button id="btnPrimary"
              class="btn-primary flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white"
              style="background: rgba(220,80,80,.85);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1V10"/>
        </svg>
        <span id="btnPrimaryText">Ke Beranda</span>
      </button>
      <button class="btn-secondary flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm text-white border border-white/15 bg-white/5"
              onclick="location.reload()">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Coba Lagi
      </button>
    </div>

    <!-- Status Bar -->
    <div class="anim-fadeup delay-6 mt-10 flex items-center gap-2 bg-white/5 border border-white/8 rounded-xl px-4 py-2.5 text-[12px] text-slate-400">
      <span id="statusDot" class="status-dot w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
      <span id="statusText">Sistem sedang memeriksa koneksi</span>
      <div class="flex gap-0.5 ml-1">
        <span class="loading-dot w-1 h-1 rounded-full bg-slate-500"></span>
        <span class="loading-dot w-1 h-1 rounded-full bg-slate-500"></span>
        <span class="loading-dot w-1 h-1 rounded-full bg-slate-500"></span>
      </div>
    </div>

    <!-- Footer -->
    <p class="anim-fadeup delay-6 mt-8 text-[11px] text-slate-600">
      Â© 2024 MTs Syafiiyah â€” Aplikasi Pembelajaran Digital
    </p>
  </div>


  <!-- ==================== THREE.JS 3D SCENE ==================== -->
  <script>
    // â”€â”€ Error data config â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const errorData = {
      '404':    { code:'404',  title:'Halaman Tidak Ditemukan',  desc:'Maaf, halaman yang kamu cari tidak tersedia di sistem pembelajaran.',        glow:'glow-red',    color:0xdc5050, sarung:0x1a3a99, kitab:0x1a7744, statusDot:'bg-red-400',    status:'Halaman tidak ditemukan di server',   btnColor:'rgba(220,80,80,.85)',   btnText:'Ke Beranda' },
      '500':    { code:'500',  title:'Kesalahan Internal Server', desc:'Server aplikasi sedang gangguan. Tim teknis kami sedang memperbaikinya.',   glow:'glow-yellow', color:0xf0b428, sarung:0x663300, kitab:0x884422, statusDot:'bg-yellow-400', status:'Tim teknis sedang menangani masalah', btnColor:'rgba(240,180,40,.85)',  btnText:'Muat Ulang' },
      '403':    { code:'403',  title:'Akses Ditolak',             desc:'Kamu tidak memiliki izin untuk mengakses halaman ini. Silakan login.',      glow:'glow-blue',   color:0x4a90f0, sarung:0x0d3a77, kitab:0x2255aa, statusDot:'bg-blue-400',   status:'Autentikasi diperlukan',              btnColor:'rgba(74,144,240,.85)', btnText:'Masuk Akun' },
      'offline':{ code:'???',  title:'Tidak Ada Koneksi',         desc:'Perangkatmu tidak terhubung ke internet. Periksa WiFi atau data seluler.',  glow:'glow-gray',   color:0x888899, sarung:0x333344, kitab:0x445566, statusDot:'bg-slate-400',  status:'Mencoba menyambungkan kembali...',    btnColor:'rgba(140,140,160,.85)',btnText:'Sambungkan' },
    };

    let currentErr = '404';
    let scene, camera, renderer, clock;
    let santriGroup, headGroup, leftArm, rightArm;
    let questionMarks = [], particles, rings = [], animFrame;

    // â”€â”€ Init â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function init3D() {
      const canvas = document.getElementById('canvas3d');
      renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
      renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
      resize();
      renderer.setClearColor(0x000000, 0);

      scene  = new THREE.Scene();
      scene.fog = new THREE.FogExp2(0x070715, 0.016);
      camera = new THREE.PerspectiveCamera(55, canvas.clientWidth / canvas.clientHeight, 0.1, 200);
      camera.position.set(0, 2, 17);
      camera.lookAt(0, 1, 0);
      clock  = new THREE.Clock();

      buildScene();
      animate();
      window.addEventListener('resize', resize);
    }

    function resize() {
      const c = document.getElementById('canvas3d');
      const W = c.clientWidth, H = c.clientHeight;
      renderer.setSize(W, H, false);
      if (camera) { camera.aspect = W / H; camera.updateProjectionMatrix(); }
    }

    // â”€â”€ Build scene â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function buildScene() {
      const e = errorData[currentErr];
      scene.fog = new THREE.FogExp2(0x070715, 0.016);

      scene.add(new THREE.AmbientLight(0x223355, 1.2));

      const main = new THREE.PointLight(e.color, 5, 40);
      main.position.set(0, 6, 6);
      scene.add(main);
      scene._main = main;

      const fill = new THREE.PointLight(0x4444bb, 1.5, 25);
      fill.position.set(-6, 0, 4);
      scene.add(fill);

      const rim = new THREE.PointLight(e.color, 2, 20);
      rim.position.set(5, -2, -4);
      scene.add(rim);

      buildGrid();
      buildParticles(e.color);
      buildRings(e.color);
      buildSantri(e);
      buildQuestionMarks(e.color);
    }

    function mat(color, emissive, opacity) {
      return new THREE.MeshPhongMaterial({
        color,
        emissive: emissive || 0x000000,
        emissiveIntensity: .25,
        transparent: opacity < 1,
        opacity: opacity != null ? opacity : 1,
      });
    }

    function buildGrid() {
      const g = new THREE.GridHelper(60, 35, 0x1a1a3a, 0x0e0e28);
      g.position.y = -4.5;
      scene.add(g);
    }

    function buildParticles(color) {
      const n = 500, pos = new Float32Array(n*3), col = new Float32Array(n*3);
      const c = new THREE.Color(color);
      for (let i = 0; i < n; i++) {
        pos[i*3]   = (Math.random()-.5)*70;
        pos[i*3+1] = (Math.random()-.5)*45;
        pos[i*3+2] = (Math.random()-.5)*30;
        col[i*3]   = c.r * Math.random() + .05;
        col[i*3+1] = c.g * Math.random() + .02;
        col[i*3+2] = c.b * Math.random() + .15;
      }
      const geo = new THREE.BufferGeometry();
      geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
      geo.setAttribute('color',    new THREE.BufferAttribute(col, 3));
      particles = new THREE.Points(geo, new THREE.PointsMaterial({ size:.1, vertexColors:true, transparent:true, opacity:.65 }));
      scene.add(particles);
    }

    function buildRings(color) {
      rings = [];
      [4.5, 6.5, 9].forEach((r, i) => {
        const m = new THREE.Mesh(
          new THREE.TorusGeometry(r, .022, 8, 80),
          new THREE.MeshBasicMaterial({ color, transparent:true, opacity:.22 - i*.06 })
        );
        m.rotation.x = Math.PI/2 + i*.25;
        m.rotation.y = i*.4;
        scene.add(m);
        rings.push(m);
      });
    }

    // â”€â”€ Santri character â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function buildSantri(e) {
      santriGroup = new THREE.Group();
      const skin = 0xc8956c, baju = 0xf0ece4, kop = 0xfafafa;

      // Torso
      santriGroup.add(obj(new THREE.CylinderGeometry(.75,.85,2.2,10), mat(baju), 0,0,0));
      // Sarung
      santriGroup.add(obj(new THREE.CylinderGeometry(.85,1.0,2.4,10), mat(e.sarung), 0,-2.2,0));
      santriGroup.add(obj(new THREE.CylinderGeometry(1.0,.75,.3,10),  mat(e.sarung), 0,-3.4,0));
      // Neck
      santriGroup.add(obj(new THREE.CylinderGeometry(.25,.28,.4,8), mat(skin), 0,1.2,0));

      // Head group
      headGroup = new THREE.Group();
      headGroup.position.set(0, 1.9, 0);

      headGroup.add(obj(new THREE.SphereGeometry(.62,14,10), mat(skin)));
      headGroup.add(obj(new THREE.CylinderGeometry(.58,.6,.35,10), mat(kop), 0,.58,0));
      headGroup.add(obj(new THREE.CylinderGeometry(.35,.58,.12,10), mat(kop), 0,.75,0));

      // Eyes
      const eyeGeo = new THREE.SphereGeometry(.09,8,8);
      headGroup.add(obj(eyeGeo, mat(0x111111), -.22,.08,.55));
      headGroup.add(obj(eyeGeo, mat(0x111111),  .22,.08,.55));

      // Brows (raised = bingung)
      const browMat = mat(0x5c3d1e);
      const bl = new THREE.Mesh(new THREE.TorusGeometry(.1,.026,4,10,Math.PI*.6), browMat);
      bl.rotation.set(Math.PI*.45, 0, -Math.PI*.3); bl.position.set(-.22,.24,.54);
      headGroup.add(bl);
      const br = bl.clone(); br.rotation.z = Math.PI*.3; br.position.set(.22,.24,.54);
      headGroup.add(br);

      // Mouth (cemberut)
      const mouth = new THREE.Mesh(new THREE.TorusGeometry(.13,.03,4,10,Math.PI), mat(0x8b3a3a));
      mouth.rotation.set(Math.PI*.4, 0, Math.PI); mouth.position.set(0,-.18,.56);
      headGroup.add(mouth);

      // Sweat drop
      headGroup.add(obj(new THREE.SphereGeometry(.058,6,6),      mat(0x88ccff,0,.7), .72,.32,.08));
      headGroup.add(obj(new THREE.CylinderGeometry(.03,.015,.18,6), mat(0x88ccff,0,.7), .73,.18,.08));

      santriGroup.add(headGroup);

      // Left arm (terangkat bingung)
      leftArm = new THREE.Group();
      leftArm.position.set(-.85,.7,0);
      const lU = obj(new THREE.CylinderGeometry(.2,.18,1.1,8), mat(baju), 0,-.55,0);
      lU.rotation.z = .45; leftArm.add(lU);
      const lL = obj(new THREE.CylinderGeometry(.16,.14,1.0,8), mat(baju), -.58,-1.18,0);
      lL.rotation.z = .75; leftArm.add(lL);
      leftArm.add(obj(new THREE.SphereGeometry(.18,8,8), mat(skin), -.98,-1.62,0));
      santriGroup.add(leftArm);

      // Right arm (pegang kitab)
      rightArm = new THREE.Group();
      rightArm.position.set(.85,.7,0);
      const rU = obj(new THREE.CylinderGeometry(.2,.18,1.1,8), mat(baju), 0,-.55,0);
      rU.rotation.z = -.45; rightArm.add(rU);
      const rL = obj(new THREE.CylinderGeometry(.16,.14,1.0,8), mat(baju), .58,-1.18,0);
      rL.rotation.z = -.75; rightArm.add(rL);
      rightArm.add(obj(new THREE.SphereGeometry(.18,8,8), mat(skin), .98,-1.62,0));

      // Kitab
      const kitab = obj(new THREE.BoxGeometry(.55,.7,.07), mat(e.kitab), 1.2,-1.45,.12);
      kitab.rotation.z = -.28;
      kitab.add(obj(new THREE.BoxGeometry(.01,.7,.075), mat(0xffffff,0,.35), .08,0,0));
      rightArm.add(kitab);
      santriGroup.add(rightArm);

      santriGroup.position.set(0,-1.4,0);
      scene.add(santriGroup);
    }

    function obj(geo, material, x=0, y=0, z=0) {
      const m = new THREE.Mesh(geo, material);
      m.position.set(x, y, z);
      return m;
    }

    // â”€â”€ Question marks (tanda tanya melayang) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const QPOS = [[1.9,3.6,1],[-2.3,4.1,.5],[.5,5.2,-.5],[-1,2.9,1.2],[2.6,2.3,0]];

    function buildQuestionMarks(color) {
      questionMarks = [];
      const c = new THREE.Color(color);
      const hexStr = `rgb(${Math.round(c.r*255)},${Math.round(c.g*255)},${Math.round(c.b*255)})`;
      QPOS.forEach((p, i) => {
        const cv = document.createElement('canvas');
        cv.width = 128; cv.height = 128;
        const ctx = cv.getContext('2d');
        ctx.font = 'bold 88px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = hexStr;
        ctx.fillText('?', 64, 68);
        const tex = new THREE.CanvasTexture(cv);
        const pl = new THREE.Mesh(
          new THREE.PlaneGeometry(.9,.9),
          new THREE.MeshBasicMaterial({ map:tex, transparent:true, depthWrite:false })
        );
        pl.position.set(p[0], p[1], p[2]);
        pl._baseY  = p[1];
        pl._phase  = i * 1.2;
        pl._speed  = .7 + i * .15;
        scene.add(pl);
        questionMarks.push(pl);
      });
    }

    // â”€â”€ Animation loop â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function animate() {
      animFrame = requestAnimationFrame(animate);
      const t = clock.getElapsedTime();

      // Santri body
      if (headGroup) {
        headGroup.rotation.z = Math.sin(t*1.1)*.22;
        headGroup.rotation.y = Math.sin(t*.7)*.18;
        headGroup.rotation.x = Math.sin(t*.9)*.07;
      }
      if (leftArm)  { leftArm.rotation.z  = Math.sin(t*1.3+1)*.28-.12; leftArm.rotation.x  = Math.sin(t*.9)*.15; }
      if (rightArm) { rightArm.rotation.z = Math.sin(t*1.1)*.2+.06;    rightArm.rotation.x = Math.sin(t*.8+.5)*.1; }
      if (santriGroup) {
        santriGroup.position.y = -1.4 + Math.sin(t*.7)*.12;
        santriGroup.rotation.y = Math.sin(t*.4)*.12;
      }

      // Question marks
      questionMarks.forEach((q,i) => {
        q.position.y = q._baseY + Math.sin(t * q._speed + q._phase) * .35;
        q.rotation.z = Math.sin(t*.6 + i) * .15;
        q.material.opacity = .55 + Math.sin(t*1.5 + i*.8) * .3;
        q.lookAt(camera.position);
      });

      // Rings
      rings.forEach((r, i) => {
        r.rotation.z = t * (.18 + i*.07);
        r.rotation.x = Math.PI/2 + Math.sin(t*.25+i)*.18;
      });

      if (particles) { particles.rotation.y = t*.035; particles.rotation.x = Math.sin(t*.04)*.08; }

      if (scene._main) {
        scene._main.position.x = Math.sin(t*.4)*4;
        scene._main.position.z = 6 + Math.cos(t*.3)*2;
        scene._main.intensity  = 4 + Math.sin(t*1.2)*.8;
      }

      camera.position.x = Math.sin(t*.12)*1.2;
      camera.position.y = 2 + Math.cos(t*.1)*.4;
      camera.lookAt(0, 1, 0);

      renderer.render(scene, camera);
    }

    // â”€â”€ Switch error â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function clearScene() {
      cancelAnimationFrame(animFrame);
      while (scene.children.length) scene.remove(scene.children[0]);
      santriGroup = headGroup = leftArm = rightArm = null;
      questionMarks = []; particles = null; rings = [];
    }

    function switchError(type, btn) {
      currentErr = type;
      const e = errorData[type];

      // Update UI text
      const code = document.getElementById('errCode');
      code.classList.remove('glow-red','glow-yellow','glow-blue','glow-gray');
      code.classList.add(e.glow);
      code.style.opacity = '0';
      setTimeout(() => { code.textContent = e.code; code.style.opacity = '1'; }, 250);
      document.getElementById('errTitle').textContent  = e.title;
      document.getElementById('errDesc').textContent   = e.desc;
      document.getElementById('statusText').textContent = e.status;
      const dot = document.getElementById('statusDot');
      dot.className = `status-dot w-2 h-2 rounded-full flex-shrink-0 ${e.statusDot}`;
      const bp = document.getElementById('btnPrimary');
      bp.style.background = e.btnColor;
      document.getElementById('btnPrimaryText').textContent = e.btnText;

      // Update tabs
      document.querySelectorAll('.tab-btn').forEach(b => {
        b.className = 'tab-btn text-[11px] px-3.5 py-1 rounded-full border border-slate-600 text-slate-400';
      });
      btn.classList.remove('border-slate-600','text-slate-400');
      btn.classList.add('active-' + type);

      // Rebuild 3D
      clearScene();
      buildScene();
      animate();
    }

    // Start
    window.addEventListener('load', init3D);
  </script>
</body>
</html>
