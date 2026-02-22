<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TSP (BEA) </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root{
            --bg: #0b1220;
            --panel: rgba(255,255,255,.06);
            --panel-strong: rgba(255,255,255,.10);
            --border: rgba(255,255,255,.12);
            --text: rgba(255,255,255,.92);
            --muted: rgba(255,255,255,.70);
            --muted2: rgba(255,255,255,.55);
            --shadow: 0 16px 40px rgba(0,0,0,.35);
            --radius: 18px;
            --radius-sm: 14px;

            --primary: #7c3aed;   /* lila */
            --primary2:#22c55e;   /* zöld */
            --danger: #ef4444;    /* piros */
            --warn: #f59e0b;      /* sárga */
        }

        *{ box-sizing: border-box; }
        html, body { height: 100%; }

        body{
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color: var(--text);
            background:
                radial-gradient(1000px 500px at 10% 10%, rgba(124,58,237,.25), transparent 55%),
                radial-gradient(900px 500px at 90% 20%, rgba(34,197,94,.18), transparent 55%),
                radial-gradient(700px 450px at 50% 110%, rgba(59,130,246,.14), transparent 55%),
                linear-gradient(180deg, #070b14 0%, #0b1220 40%, #070b14 100%);
            padding: 24px;
        }

        .page{
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        header{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 18px 6px;
        }

        .title{
            line-height: 1.1;
        }
        .title h2{
            margin: 0 0 6px;
            font-size: 22px;
            letter-spacing: .2px;
        }
        .title p{
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            max-width: 72ch;
        }

        .pill{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.06);
            border: 1px solid var(--border);
            box-shadow: 0 10px 28px rgba(0,0,0,.20);
            color: var(--muted);
            font-size: 13px;
            white-space: nowrap;
        }
        .pill b{ color: var(--text); font-weight: 650; }

        .content{
            display: grid;
            gap: 18px;
            grid-template-columns: 1fr 360px;
            align-items: start;
        }

        @media (max-width: 980px){
            .content{ grid-template-columns: 1fr; }
        }

        .card{
            background: linear-gradient(180deg, rgba(255,255,255,.075) 0%, rgba(255,255,255,.045) 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .canvasCard{
            padding: 14px;
            position: relative;
        }

        .canvasTop{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
            padding: 8px 6px 14px;
            color: var(--muted);
            font-size: 13px;
        }
        .badge{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.06);
            color: var(--muted);
        }
        .dot{
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--primary2);
            box-shadow: 0 0 0 4px rgba(34,197,94,.12);
        }

        canvas{
            width: 100%;
            height: auto;
            display:block;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.12);
            background:
                radial-gradient(1200px 600px at 20% 20%, rgba(255,255,255,.05), transparent 55%),
                rgba(0,0,0,.22);
            cursor: crosshair;
        }
        canvas:focus{ outline: none; }

        .side{
            display: grid;
            gap: 14px;
            position: sticky;
            top: 18px;
        }

        .actions{
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            padding: 14px;
        }

        .btn{
            appearance: none;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.06);
            color: var(--text);
            border-radius: 14px;
            padding: 11px 12px;
            font-weight: 650;
            cursor: pointer;
            transition: transform .08s ease, background .2s ease, border-color .2s ease, opacity .2s ease;
            user-select: none;
        }
        .btn:hover{
            background: rgba(255,255,255,.10);
            border-color: rgba(255,255,255,.18);
        }
        .btn:active{ transform: translateY(1px); }
        .btn:disabled{ opacity:.45; cursor:not-allowed; transform:none; }

        .btnPrimary{
            background: linear-gradient(135deg, rgba(124,58,237,.85), rgba(59,130,246,.55));
            border-color: rgba(124,58,237,.35);
        }
        .btnPrimary:hover{
            background: linear-gradient(135deg, rgba(124,58,237,.92), rgba(59,130,246,.62));
        }

        .btnWarn{
            background: rgba(245,158,11,.12);
            border-color: rgba(245,158,11,.30);
        }

        .btnDanger{
            background: rgba(239,68,68,.12);
            border-color: rgba(239,68,68,.30);
        }

        .panelBlock{
            padding: 14px;
        }

        .panelTitle{
            font-size: 13px;
            color: var(--muted2);
            letter-spacing: .2px;
            margin-bottom: 10px;
        }

        .stats{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat{
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(0,0,0,.15);
        }
        .stat .k{
            color: var(--muted2);
            font-size: 12px;
            margin-bottom: 6px;
        }
        .stat .v{
            font-size: 18px;
            font-weight: 750;
            letter-spacing: .2px;
        }

        .hint{
            margin-top: 10px;
            display:flex;
            gap: 8px;
            align-items:center;
            color: var(--muted);
            font-size: 13px;
        }
        .hint .chip{
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
            color: var(--muted);
        }

        .routeBox{
            max-height: 220px;
            overflow: auto;
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.10);
            background: rgba(0,0,0,.16);
            white-space: pre-wrap;
            word-break: break-word;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            line-height: 1.45;
            color: rgba(255,255,255,.86);
        }

        /* Scrollbar (webkit) */
        .routeBox::-webkit-scrollbar{ width: 10px; }
        .routeBox::-webkit-scrollbar-thumb{
            background: rgba(255,255,255,.12);
            border-radius: 999px;
            border: 2px solid rgba(0,0,0,.20);
        }
        .routeBox::-webkit-scrollbar-track{ background: transparent; }
    </style>
</head>

<body>
<div class="page">
    <header class="card" style="padding:0;">
        <div style="padding:18px 18px 16px; display:flex; gap:16px; align-items:flex-start; justify-content:space-between; flex-wrap:wrap;">
            <div class="title">
                <h2>TSP (BEA) – kattintásos pontfelvétel</h2>
                <p>
                    Bal klikk: pont felvétel. <b>Undo</b> törli az utolsót. <b>Solve</b> elküldi backendnek és kirajzolja az útvonalat.
                </p>
            </div>
            <div class="pill">
                <span class="dot"></span>
                Grid: <b>25px</b> • Canvas: <b>900×600</b>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="card canvasCard">
            <div class="canvasTop">
                <span class="badge">Kattints a vászonra városok hozzáadásához</span>
                <span class="badge">Route mindig zárt kör</span>
            </div>
            <canvas id="cv" width="900" height="600" tabindex="0"></canvas>
        </div>

        <aside class="side">
            <div class="card">
                <div class="actions">
                    <button id="solveBtn" class="btn btnPrimary" disabled>Solve</button>
                    <button id="undoBtn" class="btn btnWarn" disabled>Undo</button>
                    <button id="resetBtn" class="btn btnDanger" disabled>Reset</button>
                </div>
            </div>

            <div class="card">
                <div class="panelBlock">
                    <div class="panelTitle">Statisztika</div>
                    <div class="stats">
                        <div class="stat">
                            <div class="k">Városok száma</div>
                            <div class="v"><span id="count">0</span></div>
                        </div>
                        <div class="stat">
                            <div class="k">Távolság</div>
                            <div class="v"><span id="fitness">–</span></div>
                        </div>
                    </div>
                    <div class="hint">
                        <span class="chip">tipp</span>
                        <span>Legalább <b>4</b> város felvétele szükséges.</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="panelBlock">
                    <div class="panelTitle">Útvonal</div>
                    <pre id="route" class="routeBox"></pre>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    (() => {
        const gridSize = 25;

        const canvas = document.getElementById('cv');
        const ctx = canvas.getContext('2d');

        const solveBtn = document.getElementById('solveBtn');
        const undoBtn = document.getElementById('undoBtn');
        const resetBtn = document.getElementById('resetBtn');

        const countEl = document.getElementById('count');
        const fitnessEl = document.getElementById('fitness');
        const routeEl = document.getElementById('route');

        const cities = []; // {name, x, y}
        let bestRoute = null; // [indexek]
        let bestFitness = null;

        function snap(v) {
            return Math.round(v / gridSize) * gridSize;
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function updateUi() {
            countEl.textContent = cities.length;
            fitnessEl.textContent = bestFitness == null ? '–' : bestFitness.toFixed(4);
            routeEl.textContent = bestRoute
                ? bestRoute.map(i => cities[i]?.name).join(' → ')
                : '';

            solveBtn.disabled = cities.length < 4;
            undoBtn.disabled = cities.length < 1;
            resetBtn.disabled = cities.length < 1 && !bestRoute;
        }

        function clear() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function drawGrid() {
            ctx.save();
            ctx.globalAlpha = 0.18;
            ctx.lineWidth = 1;

            ctx.strokeStyle = 'rgba(255,255,255,.28)';

            for (let x = 0; x <= canvas.width; x += gridSize) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }

            for (let y = 0; y <= canvas.height; y += gridSize) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }

            ctx.restore();
        }

        function drawCities() {
            ctx.save();
            ctx.globalAlpha = 1;

            for (let i = 0; i < cities.length; i++) {
                const c = cities[i];

                ctx.beginPath();
                ctx.fillStyle = 'rgba(255,255,255,.92)';
                ctx.arc(c.x, c.y, 5, 0, Math.PI * 2);
                ctx.fill();

                ctx.beginPath();
                ctx.fillStyle = 'rgba(124,58,237,.35)';
                ctx.arc(c.x, c.y, 9, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = 'rgba(255,255,255,.90)';
                ctx.font = '12px system-ui, Arial';
                ctx.fillText(c.name, c.x + 10, c.y - 10);
            }

            ctx.restore();
        }

        function drawRoute(route) {
            if (!route || route.length < 2) return;

            ctx.save();
            ctx.lineWidth = 2.5;
            ctx.strokeStyle = 'rgba(34,197,94,.90)';

            // soft glow
            ctx.shadowColor = 'rgba(34,197,94,.35)';
            ctx.shadowBlur = 10;

            ctx.beginPath();
            const first = cities[route[0]];
            ctx.moveTo(first.x, first.y);

            for (let i = 1; i < route.length; i++) {
                const p = cities[route[i]];
                ctx.lineTo(p.x, p.y);
            }

            ctx.lineTo(first.x, first.y);
            ctx.stroke();

            ctx.restore();
        }

        function redraw() {
            clear();
            drawGrid();
            drawCities();
            if (bestRoute) drawRoute(bestRoute);
        }

        canvas.addEventListener('click', (e) => {
            const rect = canvas.getBoundingClientRect();

            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            let x = (e.clientX - rect.left) * scaleX;
            let y = (e.clientY - rect.top) * scaleY;

            x = snap(x);
            y = snap(y);

            const name = 'C' + (cities.length + 1);
            cities.push({ name, x, y });

            bestRoute = null;
            bestFitness = null;

            updateUi();
            redraw();
        });

        undoBtn.addEventListener('click', () => {
            if (cities.length > 0) cities.pop();
            bestRoute = null;
            bestFitness = null;
            updateUi();
            redraw();
        });

        resetBtn.addEventListener('click', () => {
            cities.length = 0;
            bestRoute = null;
            bestFitness = null;
            updateUi();
            redraw();
        });

        solveBtn.addEventListener('click', async () => {
            if (cities.length < 4) return;

            solveBtn.disabled = true;
            solveBtn.textContent = 'Solving...';

            try {
                const res = await fetch("{{ route('tsp.solve') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ cities }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data?.message ?? 'Solve failed');
                }

                bestRoute = data.bestRoute;
                bestFitness = data.bestFitness;

                updateUi();
                redraw();
            } catch (err) {
                alert(err?.message ?? String(err));
            } finally {
                solveBtn.disabled = false;
                solveBtn.textContent = 'Solve';
            }
        });

        updateUi();
        redraw();
    })();
</script>
</body>
</html>
