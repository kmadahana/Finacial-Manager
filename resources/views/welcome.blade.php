<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Budget Tracker — Spend Smarter. Save More.</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #07090f;
            --bg2:       #0d1117;
            --card:      #111620;
            --border:    rgba(255,255,255,0.07);
            --green:     #00e676;
            --green-dim: rgba(0,230,118,0.12);
            --green-glow:rgba(0,230,118,0.25);
            --red:       #ff5252;
            --red-dim:   rgba(255,82,82,0.12);
            --text:      #f0f4ff;
            --muted:     #7a8499;
            --font-head: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-weight: 400;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 12px;
            left: 20px; right: 20px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 40px;
            background: rgba(7,9,15,0.80);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 16px;
        }

        .nav-logo {
            font-family: var(--font-head);
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 12px var(--green);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%,100% { box-shadow: 0 0 12px var(--green); }
            50%      { box-shadow: 0 0 28px var(--green), 0 0 48px var(--green-glow); }
        }

        .nav-links { display: flex; align-items: center; gap: 12px; }

        .btn-ghost {
            background: transparent;
            border: none;
            color: var(--muted);
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 500;
            padding: 9px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s;
        }
        .btn-ghost:hover { color: var(--text); }

        .btn-primary {
            background: var(--green);
            color: #07090f;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 700;
            padding: 9px 22px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: box-shadow 0.2s, transform 0.15s;
        }
        .btn-primary:hover {
            box-shadow: 0 0 28px var(--green-glow);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            font-family: var(--font-body);
            font-size: 16px;
            font-weight: 500;
            padding: 14px 32px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: border-color 0.2s, background 0.2s;
        }
        .btn-outline:hover {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.04);
        }

        .btn-large { font-size: 16px; padding: 14px 32px; border-radius: 10px; }

        /* ── HERO ── */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 130px 24px 80px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center top;
            filter: brightness(0.22) saturate(0.6);
            z-index: 0;
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(7,9,15,0.1) 0%,
                rgba(7,9,15,0.3) 40%,
                rgba(7,9,15,0.85) 80%,
                var(--bg) 100%
            );
        }

        /* floating person image on hero */
        .hero-person {
            position: absolute;
            right: 4%;
            bottom: 0;
            height: 82%;
            width: auto;
            object-fit: cover;
            object-position: top center;
            z-index: 1;
            mask-image: linear-gradient(to top, transparent 0%, black 18%, black 85%, transparent 100%);
            -webkit-mask-image: linear-gradient(to top, transparent 0%, black 18%, black 85%, transparent 100%);
            opacity: 0.88;
            animation: fadeUp 1s 0.5s ease both;
        }

        /* floating notification card */
        .hero-toast {
            position: absolute;
            right: calc(4% + 180px);
            bottom: 18%;
            background: rgba(255,255,255,0.97);
            color: #07090f;
            border-radius: 14px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            z-index: 2;
            animation: fadeUp 0.9s 0.9s ease both;
            white-space: nowrap;
        }

        .toast-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #e8faf0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .toast-title {
            font-family: var(--font-head);
            font-size: 14px;
            font-weight: 700;
            color: #111;
        }

        .toast-sub {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

        .hero-glow {
            position: absolute;
            top: 8%;
            left: 50%;
            transform: translateX(-50%);
            width: 900px; height: 500px;
            background: radial-gradient(ellipse, rgba(0,230,118,0.07) 0%, rgba(255,82,82,0.03) 50%, transparent 70%);
            pointer-events: none;
            z-index: 1;
        }

        .hero-badge {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-dim);
            border: 1px solid rgba(0,230,118,0.25);
            color: var(--green);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 100px;
            margin-bottom: 32px;
            animation: fadeUp 0.6s ease both;
        }

        .badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 2s ease-in-out infinite;
        }

        h1 {
            position: relative;
            z-index: 2;
            font-family: var(--font-head);
            font-size: clamp(48px, 7vw, 86px);
            font-weight: 800;
            line-height: 1.0;
            letter-spacing: -2px;
            margin-bottom: 24px;
            animation: fadeUp 0.7s 0.1s ease both;
        }

        h1 .grad {
            background: linear-gradient(135deg, var(--green) 0%, #69ffb8 40%, var(--red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            position: relative;
            z-index: 2;
            font-size: 18px;
            color: var(--muted);
            max-width: 520px;
            line-height: 1.75;
            margin-bottom: 44px;
            animation: fadeUp 0.7s 0.2s ease both;
        }

        .hero-cta {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: center;
            margin-bottom: 72px;
            animation: fadeUp 0.7s 0.3s ease both;
        }

        /* ── MOCKUP ── */
        .hero-mockup {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeUp 0.8s 0.45s ease both;
        }

        .mockup-glow {
            position: absolute;
            inset: -60px;
            background: radial-gradient(ellipse at center, rgba(0,230,118,0.06) 0%, transparent 65%);
            pointer-events: none;
        }

        .mockup-frame {
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.04);
        }

        .mockup-bar {
            background: #0d1117;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
        }

        .m-dot { width: 10px; height: 10px; border-radius: 50%; }

        .mockup-body {
            padding: 28px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .mock-card {
            background: rgba(255,255,255,0.025);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        .mock-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .mock-val {
            font-family: var(--font-head);
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
        }
        .mock-val.g { color: var(--green); }
        .mock-val.r { color: var(--red); }
        .mock-val.b { color: #64b5f6; }

        .mock-sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }
        .mock-sub em { color: var(--green); font-style: normal; }
        .mock-sub em.bad { color: var(--red); }

        .mock-chart {
            grid-column: span 3;
            background: rgba(255,255,255,0.015);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 20px 0;
            height: 140px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(to top, var(--green), rgba(0,230,118,0.25));
            animation: growBar 0.8s ease both;
            transform-origin: bottom;
        }

        .bar.spent {
            background: linear-gradient(to top, var(--red), rgba(255,82,82,0.2));
        }

        @keyframes growBar {
            from { transform: scaleY(0); opacity: 0; }
            to   { transform: scaleY(1); opacity: 1; }
        }

        .bar:nth-child(1)  { height: 42%; animation-delay: 0.5s; }
        .bar:nth-child(2)  { height: 60%; animation-delay: 0.55s; }
        .bar:nth-child(3)  { height: 38%; animation-delay: 0.60s; }
        .bar:nth-child(4)  { height: 75%; animation-delay: 0.65s; }
        .bar:nth-child(5)  { height: 52%; animation-delay: 0.70s; }
        .bar:nth-child(6)  { height: 88%; animation-delay: 0.75s; }
        .bar:nth-child(7)  { height: 58%; animation-delay: 0.80s; }
        .bar:nth-child(8)  { height: 72%; animation-delay: 0.85s; }
        .bar:nth-child(9)  { height: 45%; animation-delay: 0.90s; }
        .bar:nth-child(10) { height: 82%; animation-delay: 0.95s; }
        .bar:nth-child(11) { height: 66%; animation-delay: 1.00s; }
        .bar:nth-child(12) { height: 96%; animation-delay: 1.05s; background: linear-gradient(to top, #69ffb8, rgba(105,255,184,0.3)); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── SECTIONS ── */
        section { position: relative; z-index: 1; }

        .inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 48px;
        }

        .tag {
            display: inline-block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--green);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .sec-title {
            font-family: var(--font-head);
            font-size: clamp(30px, 4vw, 50px);
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .sec-sub {
            font-size: 17px;
            color: var(--muted);
            line-height: 1.75;
        }

        /* ── STATS ── */
        .stats {
            padding: 60px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .stat {
            text-align: center;
            padding: 28px 16px;
            border-right: 1px solid var(--border);
        }
        .stat:last-child { border-right: none; }

        .stat-num {
            font-family: var(--font-head);
            font-size: 44px;
            font-weight: 800;
            color: var(--green);
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label { font-size: 13px; color: var(--muted); }

        /* ── FEATURES ── */
        .features { padding: 120px 0; }

        .features-head {
            text-align: center;
            margin-bottom: 64px;
        }
        .features-head .sec-sub { max-width: 480px; margin: 0 auto; }

        .feat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.3s, transform 0.3s;
        }

        .feat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--green), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feat-card:hover { border-color: rgba(0,230,118,0.2); transform: translateY(-4px); }
        .feat-card:hover::before { opacity: 1; }

        .feat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: var(--green-dim);
            border: 1px solid rgba(0,230,118,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .feat-card:nth-child(2) .feat-icon,
        .feat-card:nth-child(5) .feat-icon {
            background: var(--red-dim);
            border-color: rgba(255,82,82,0.2);
        }

        .feat-title {
            font-family: var(--font-head);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feat-desc { font-size: 14px; color: var(--muted); line-height: 1.7; }

        /* ── SPLIT ── */
        .split { padding: 100px 0; }

        .split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .split-img {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            position: relative;
        }

        .split-img img {
            width: 100%; height: 400px;
            object-fit: cover;
            display: block;
            filter: brightness(0.8) saturate(0.85);
        }

        .split-img::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(0,230,118,0.06), rgba(255,82,82,0.04) 60%, transparent);
        }

        .checklist {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 28px;
        }

        .checklist li {
            display: flex;
            gap: 12px;
            font-size: 15px;
            color: var(--muted);
        }

        .checklist li::before {
            content: '✓';
            color: var(--green);
            font-weight: 700;
            font-size: 13px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        /* ── PHOTO GRID ── */
        .photos { padding: 100px 0; }

        .photos-head { text-align: center; margin-bottom: 52px; }

        .photo-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            grid-template-rows: 260px 260px;
            gap: 14px;
        }

        .photo-card {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }

        .photo-card img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            filter: brightness(0.7) saturate(0.8);
            transition: transform 0.5s ease, filter 0.4s;
        }

        .photo-card:hover img {
            transform: scale(1.05);
            filter: brightness(0.85) saturate(1);
        }

        .photo-card-label {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 20px 20px 16px;
            background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);
            font-family: var(--font-head);
            font-size: 15px;
            font-weight: 700;
            color: white;
        }

        .photo-card:first-child { grid-row: span 2; }

        /* ── CTA ── */
        .cta { padding: 120px 0; }

        /* CTA person image */
        .cta-box {
            background: linear-gradient(135deg, rgba(0,230,118,0.05), rgba(255,82,82,0.04));
            border: 1px solid rgba(0,230,118,0.12);
            border-radius: 24px;
            padding: 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 360px;
            align-items: stretch;
            min-height: 380px;
        }

        .cta-content {
            padding: 80px 60px;
            text-align: left;
            position: relative;
            z-index: 1;
        }

        .cta-content .sec-sub { max-width: 400px; margin: 0 0 36px; }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -120px; left: 0;
            width: 500px; height: 320px;
            background: radial-gradient(ellipse, rgba(0,230,118,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-img-wrap {
            position: relative;
            overflow: hidden;
            border-radius: 0 24px 24px 0;
        }

        .cta-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            filter: brightness(0.75) saturate(0.9);
            display: block;
        }

        .cta-img-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(7,9,15,0.5) 0%, transparent 40%),
                        linear-gradient(135deg, rgba(0,230,118,0.08), transparent 60%);
        }

        @media (max-width: 960px) {
            .cta-box { grid-template-columns: 1fr; }
            .cta-img-wrap { height: 260px; border-radius: 0 0 24px 24px; }
            .cta-content { padding: 60px 40px; text-align: center; }
            .cta-content .sec-sub { margin: 0 auto 36px; }
        }

        /* ── FOOTER ── */
        footer {
            border-top: 1px solid var(--border);
            padding: 36px 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-logo {
            font-family: var(--font-head);
            font-size: 18px;
            font-weight: 800;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-copy { font-size: 13px; color: var(--muted); }

        /* ── RESPONSIVE ── */
        @media (max-width: 960px) {
            nav { padding: 16px 24px; }
            .inner { padding: 0 24px; }
            .mockup-body { grid-template-columns: 1fr 1fr; }
            .mock-chart { grid-column: span 2; }
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .stat:nth-child(2) { border-right: none; }
            .feat-grid { grid-template-columns: 1fr 1fr; }
            .split-grid { grid-template-columns: 1fr; gap: 40px; }
            .photo-grid { grid-template-columns: 1fr 1fr; grid-template-rows: auto; }
            .photo-card:first-child { grid-row: span 1; }
            .photo-card { height: 220px; }
            .hero-person { display: none; }
            .hero-toast { display: none; }
        }

        @media (max-width: 600px) {
            nav { left: 10px; right: 10px; padding: 14px 20px; }
            .feat-grid { grid-template-columns: 1fr; }
            .photo-grid { grid-template-columns: 1fr; }
            footer { flex-direction: column; gap: 10px; text-align: center; }
            .hero-cta { flex-direction: column; }
        }
    </style>
</head>
<body>

<!-- ── NAV ── -->
<nav>
    <a href="/" class="nav-logo">
        <span class="logo-dot"></span>
        Budget Tracker
    </a>
    <div class="nav-links">
        <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
        <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-glow"></div>

    <!-- floating person -->
    
    <!-- floating notification -->
    <div class="hero-toast">
        <div class="toast-icon">✅</div>
        <div>
            <div class="toast-title">Budget Goal Reached!</div>
            <div class="toast-sub">Saved KSh 15,000 this month</div>
        </div>
    </div>

    <div class="hero-badge">
        <span class="badge-dot"></span>
        Smart money management
    </div>

    <h1>Track. Save.<br><span class="grad">Stay in Control.</span></h1>

    <p class="hero-sub">
        Stop guessing where your money goes. Budget Tracker helps you track every shilling, visualise spending habits, and build real savings — all in one place.
    </p>

    <div class="hero-cta">
        <a href="{{ route('register') }}" class="btn-primary btn-large">Start Free Today</a>
        <a href="{{ route('login') }}" class="btn-outline">Sign In</a>
    </div>

    <div class="hero-mockup">
        <div class="mockup-glow"></div>
        <div class="mockup-frame">
            <div class="mockup-bar">
                <div class="m-dot" style="background:#ff5f57;"></div>
                <div class="m-dot" style="background:#febc2e;"></div>
                <div class="m-dot" style="background:#28c840;"></div>
            </div>
            <div class="mockup-body">
                <div class="mock-card">
                    <div class="mock-label">Total Balance</div>
                    <div class="mock-val g">KSh 284,500</div>
                    <div class="mock-sub"><em>↑ 12.4%</em> this month</div>
                </div>
                <div class="mock-card">
                    <div class="mock-label">Total Spent</div>
                    <div class="mock-val r">KSh 43,200</div>
                    <div class="mock-sub"><em class="bad">38 transactions</em></div>
                </div>
                <div class="mock-card">
                    <div class="mock-label">Savings Goal</div>
                    <div class="mock-val b">68%</div>
                    <div class="mock-sub"><em>↑ 5%</em> ahead of target</div>
                </div>
                <div class="mock-chart">
                    <div class="bar"></div>
                    <div class="bar spent"></div>
                    <div class="bar"></div>
                    <div class="bar spent"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar spent"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar spent"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── STATS ── -->
<div class="stats">
    <div class="inner">
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-num">50K+</div>
                <div class="stat-label">Active users</div>
            </div>
            <div class="stat">
                <div class="stat-num">KSh 2B</div>
                <div class="stat-label">Transactions tracked</div>
            </div>
            <div class="stat">
                <div class="stat-num">99.9%</div>
                <div class="stat-label">Uptime guaranteed</div>
            </div>
            <div class="stat">
                <div class="stat-num">4.9★</div>
                <div class="stat-label">Average rating</div>
            </div>
        </div>
    </div>
</div>

<!-- ── FEATURES ── -->
<section class="features">
    <div class="inner">
        <div class="features-head">
            <span class="tag">Features</span>
            <h2 class="sec-title">Everything you need<br>to spend smarter</h2>
            <p class="sec-sub">Built for anyone who wants a clearer picture of their finances — without the spreadsheet headaches.</p>
        </div>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon">📊</div>
                <div class="feat-title">Live Analytics</div>
                <p class="feat-desc">Real-time charts and breakdowns of every transaction, category, and spending pattern.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">🔔</div>
                <div class="feat-title">Spending Alerts</div>
                <p class="feat-desc">Get notified when you're close to budget limits or when unusual spending is detected.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">🎯</div>
                <div class="feat-title">Savings Goals</div>
                <p class="feat-desc">Set targets, track progress visually, and get suggestions on how to hit them faster.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">🔒</div>
                <div class="feat-title">Bank-Level Security</div>
                <p class="feat-desc">Your data is encrypted end-to-end. OTP verification on every sensitive action.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">📱</div>
                <div class="feat-title">Works Everywhere</div>
                <p class="feat-desc">Fully responsive across desktop, tablet and mobile — manage money anywhere.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">📤</div>
                <div class="feat-title">Export Reports</div>
                <p class="feat-desc">Download monthly or custom-range reports as PDF or CSV in one click.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── SPLIT ── -->
<section class="split">
    <div class="inner">
        <div class="split-grid">
            <div class="split-img">
                <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=900&auto=format&fit=crop" alt="Finance analytics on screen"/>
            </div>
            <div>
                <span class="tag">Built for clarity</span>
                <h2 class="sec-title">See exactly where your money goes</h2>
                <p class="sec-sub">No guesswork. No spreadsheets. Just clean visual breakdowns that make sense at a glance.</p>
                <ul class="checklist">
                    <li>Auto-categorised transactions the moment they happen</li>
                    <li>Monthly comparisons so you spot trends easily</li>
                    <li>Spending heatmaps by day, week, and category</li>
                    <li>Custom budgets that adapt to your lifestyle</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ── PHOTO GRID ── -->
<section class="photos">
    <div class="inner">
        <div class="photos-head">
            <span class="tag">The bigger picture</span>
            <h2 class="sec-title">Money touches everything.<br>We help you track all of it.</h2>
        </div>
        <div class="photo-grid">
            <div class="photo-card">
                <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=900&auto=format&fit=crop" alt="Finance dashboard overview"/>
                <div class="photo-card-label">Your complete financial picture</div>
            </div>
            <div class="photo-card">
                <img src="https://images.unsplash.com/photo-1579621970795-87facc2f976d?w=600&auto=format&fit=crop" alt="Savings jar"/>
                <div class="photo-card-label">Build real savings habits</div>
            </div>
            <div class="photo-card">
                <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&auto=format&fit=crop" alt="Security lock"/>
                <div class="photo-card-label">Secured & private</div>
            </div>
            <div class="photo-card">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop" alt="Analytics charts"/>
                <div class="photo-card-label">Analytics that actually help</div>
            </div>
            <div class="photo-card">
                <img src="https://images.unsplash.com/photo-1518458028785-8fbcd101ebb9?w=600&auto=format&fit=crop" alt="Goal setting"/>
                <div class="photo-card-label">Reach your goals faster</div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta">
    <div class="inner">
        <div class="cta-box">
            <div class="cta-content">
                <span class="tag">Get started today</span>
                <h2 class="sec-title">Ready to take<br>control?</h2>
                <p class="sec-sub">Join thousands already using Budget Tracker to cut waste and build better money habits. Free to start, no credit card needed.</p>
                <a href="{{ route('register') }}" class="btn-primary btn-large">Create Free Account</a>
            </div>
            <div class="cta-img-wrap">
                <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=600&auto=format&fit=crop&q=80" alt="Person managing finances confidently"/>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="footer-logo">
        <span class="logo-dot" style="width:8px;height:8px;"></span>
        Budget Tracker
    </div>
    <div class="footer-copy">© {{ date('Y') }} Budget Tracker — Built to help you save more.</div>
</footer>

</body>
</html>
