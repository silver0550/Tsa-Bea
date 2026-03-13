<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LL(1) Parser</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.16), transparent 30%),
                linear-gradient(135deg, #0f172a, #111827 45%, #1e293b);
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .wrapper {
            width: 100%;
            max-width: 920px;
        }

        .card {
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 32px 32px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.16);
            color: #c7d2fe;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 32px;
            line-height: 1.15;
            color: #f8fafc;
        }

        .subtitle {
            margin: 0;
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
            max-width: 700px;
        }

        .card-body {
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 600;
        }

        .input-wrap {
            position: relative;
        }

        input[type="text"] {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            color: #f8fafc;
            border-radius: 16px;
            padding: 16px 18px;
            font-size: 16px;
            outline: none;
            transition: 0.2s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        input[type="text"]::placeholder {
            color: #64748b;
        }

        input[type="text"]:focus {
            border-color: rgba(99, 102, 241, 0.7);
            box-shadow:
                0 0 0 4px rgba(99, 102, 241, 0.14),
                inset 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .helper {
            margin-top: 10px;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        button {
            border: 0;
            border-radius: 16px;
            padding: 14px 22px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.28);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 36px rgba(99, 102, 241, 0.34);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .section {
            margin-top: 28px;
            border-radius: 18px;
            padding: 20px 22px;
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .section-title {
            margin: 0 0 14px;
            font-size: 17px;
            font-weight: 700;
            color: #f8fafc;
        }

        .result-box {
            background: rgba(16, 185, 129, 0.10);
            border-color: rgba(16, 185, 129, 0.22);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.10);
            border-color: rgba(239, 68, 68, 0.22);
        }

        .result-value {
            font-size: 28px;
            font-weight: 800;
            color: #ecfeff;
            word-break: break-word;
        }

        .error-message {
            color: #fecaca;
            line-height: 1.6;
        }

        .expression-preview {
            margin-top: 10px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .derivation-box {
            background: rgba(255, 255, 255, 0.035);
        }

        .derivation-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 10px;
        }

        .derivation-list li {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #dbeafe;
            font-family: "Courier New", monospace;
            font-size: 14px;
            overflow-x: auto;
        }

        .examples {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .example-item {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            font-family: "Courier New", monospace;
            font-size: 14px;
        }

        .footer-note {
            margin-top: 24px;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            .card-header,
            .card-body {
                padding: 22px;
            }

            h1 {
                font-size: 26px;
            }

            .result-value {
                font-size: 22px;
            }

            .actions {
                flex-direction: column;
            }

            button {
                width: 100%;
            }
        }
        .derivation-flow {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .derivation-item {
            padding: 8px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            font-family: "Courier New", monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="card-header">
            <div class="badge">Fordítóprogramok · LL(1)</div>
            <h1>Kifejezés elemző és kiértékelő</h1>
            <p class="subtitle">
                Adj meg egy aritmetikai kifejezést. A rendszer támogatja a
                <strong>+ - * /</strong> műveleteket, a zárójeleket, valamint a
                <strong>sin(...)</strong> és <strong>cos(...)</strong> függvényeket is.
            </p>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('ll1.evaluate') }}">
                @csrf

                <div class="form-group">
                    <label for="expression">Kifejezés</label>

                    <div class="input-wrap">
                        <input
                            id="expression"
                            type="text"
                            name="expression"
                            placeholder="pl.: sin(0)+cos(0) vagy 2*(3+4)"
                            value="{{ old('expression', $expression ?? '') }}"
                            required
                        >
                    </div>

                    <div class="helper">
                        Példák: <code>12+3*5</code>, <code>sin(0)+cos(0)</code>, <code>2*sin(3+1)</code>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-primary">Kiértékelés</button>
                    <button type="button" class="btn-secondary" onclick="document.getElementById('expression').value='sin(0)+cos(0)'">
                        Minta betöltése
                    </button>
                </div>
            </form>

            @if(isset($value))
                <div class="section result-box">
                    <h2 class="section-title">Eredmény</h2>
                    <div class="result-value">{{ $value }}</div>

                    @if(isset($expression))
                        <div class="expression-preview">
                            Feldolgozott kifejezés: <strong>{{ $expression }}</strong>
                        </div>
                    @endif
                </div>
            @endif

            @if(isset($error))
                <div class="section error-box">
                    <h2 class="section-title">Hiba történt</h2>
                    <div class="error-message">{{ $error }}</div>

                    @if(isset($expression))
                        <div class="expression-preview">
                            Érintett kifejezés: <strong>{{ $expression }}</strong>
                        </div>
                    @endif
                </div>
            @endif

            @if(isset($productions) && is_array($productions) && count($productions))
                <div class="section derivation-box">
                    <h2 class="section-title">Levezetés</h2>

                    <div class="derivation-flow">
                        @foreach($productions as $production)
                            <span class="derivation-item">
                                {{ $production }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="examples">
                <div class="example-item">12+3*5</div>
                <div class="example-item">sin(0)+cos(0)</div>
                <div class="example-item">2*(3+4)</div>
                <div class="example-item">10/(2+3)</div>
            </div>

            <div class="footer-note">
                Megjegyzés: a PHP beépített <strong>sin()</strong> és <strong>cos()</strong>
                függvényei radiánban számolnak.
            </div>
        </div>
    </div>
</div>
</body>
</html>
