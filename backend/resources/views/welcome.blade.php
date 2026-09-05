<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} — API &amp; Admin</title>
        <meta name="robots" content="noindex, nofollow">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            :root {
                --brand-primary: #000066;
                --brand-secondary: #ffff00;
                --brand-tertiary: #ffffff;
                --muted: color-mix(in srgb, var(--brand-primary) 62%, var(--brand-tertiary));
                --border: color-mix(in srgb, var(--brand-primary) 14%, var(--brand-tertiary));
                --surface: color-mix(in srgb, var(--brand-primary) 4%, var(--brand-tertiary));
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                background:
                    radial-gradient(60rem 32rem at 100% -10%, color-mix(in srgb, var(--brand-secondary) 16%, transparent), transparent 60%),
                    radial-gradient(50rem 28rem at -10% 110%, color-mix(in srgb, var(--brand-primary) 7%, transparent), transparent 60%),
                    var(--brand-tertiary);
                color: var(--brand-primary);
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                -webkit-font-smoothing: antialiased;
            }

            .shell {
                min-height: 100dvh;
                display: flex;
                flex-direction: column;
            }

            .accent-bar {
                height: 4px;
                background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
            }

            header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--border);
            }

            header img { display: block; height: 28px; width: auto; }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.3rem 0.7rem;
                border-radius: 999px;
                background: color-mix(in srgb, var(--brand-secondary) 25%, var(--brand-tertiary));
                color: var(--brand-primary);
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .badge::before {
                content: '';
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: #16a34a;
            }

            main {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem 1.5rem;
            }

            .card {
                max-width: 34rem;
                width: 100%;
                text-align: center;
                background: color-mix(in srgb, var(--brand-tertiary) 92%, transparent);
                border: 1px solid var(--border);
                border-radius: 1rem;
                padding: 2.75rem 2.5rem;
                box-shadow: 0 24px 60px -30px color-mix(in srgb, var(--brand-primary) 30%, transparent);
                backdrop-filter: blur(6px);
            }

            .eyebrow {
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
            }

            h1 {
                margin: 0.6rem 0 0.75rem;
                font-size: 2rem;
                font-weight: 800;
                line-height: 1.15;
                letter-spacing: -0.01em;
            }

            p.lede {
                margin: 0 auto;
                max-width: 30rem;
                color: var(--muted);
                font-size: 1.02rem;
                line-height: 1.6;
            }

            .actions {
                margin-top: 2rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                justify-content: center;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.7rem 1.4rem;
                border-radius: 0.5rem;
                font-weight: 600;
                font-size: 0.92rem;
                text-decoration: none;
                border: 1px solid transparent;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .btn:hover { transform: translateY(-1px); }

            .btn-primary {
                background: var(--brand-primary);
                color: var(--brand-tertiary);
                box-shadow: 0 8px 20px -6px color-mix(in srgb, var(--brand-primary) 45%, transparent);
            }

            .btn-secondary {
                background: var(--brand-tertiary);
                color: var(--brand-primary);
                border-color: var(--border);
            }

            .meta {
                margin-top: 3rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem 1.5rem;
                font-size: 0.8rem;
                color: var(--muted);
            }

            .meta code {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 0.3rem;
                padding: 0.1rem 0.4rem;
                font-size: 0.78rem;
            }

            footer {
                padding: 1.25rem 1.5rem;
                text-align: center;
                font-size: 0.78rem;
                color: var(--muted);
                border-top: 1px solid var(--border);
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <div class="accent-bar"></div>
            <header>
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
                <span class="badge">Service online</span>
            </header>

            <main>
                <div class="card">
                    <p class="eyebrow">API &amp; Admin Backend</p>
                    <h1>{{ config('app.name') }}</h1>
                    <p class="lede">
                        This host serves the REST API and admin panel for the Society of
                        Women Accountants of Nigeria, Abuja Chapter. The public website is
                        a separate application.
                    </p>

                    <div class="actions">
                        <a class="btn btn-primary" href="{{ $frontendUrl }}">
                            Visit the website
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>
                        </a>
                        <a class="btn btn-secondary" href="{{ url('/admin') }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            Admin panel
                        </a>
                    </div>

                    <div class="meta">
                        <span>Laravel <code>{{ app()->version() }}</code></span>
                        <span>PHP <code>{{ phpversion() }}</code></span>
                        <span>Environment <code>{{ app()->environment() }}</code></span>
                    </div>
                </div>
            </main>

            <footer>
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </body>
</html>
