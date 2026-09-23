<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PropManage' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f3f4f6; color: #111827; }
        header { background: #0f766e; color: #fff; padding: .75rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #ccfbf1; text-decoration: none; margin-right: 1rem; font-size: .9rem; }
        nav.top { display:flex; gap:.5rem; flex-wrap:wrap; }
        main { max-width: 1100px; margin: 1.5rem auto; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin: 1rem 0; }
        th, td { border: 1px solid #e5e7eb; padding: .5rem .75rem; text-align: left; font-size: .9rem; }
        th { background: #f9fafb; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: .5rem; padding: 1.25rem; margin-bottom: 1rem; }
        .btn { display:inline-block; background:#0f766e; color:#fff; border:none; padding:.45rem .9rem; border-radius:.375rem; cursor:pointer; text-decoration:none; font-size:.85rem; }
        .btn-danger { background:#b91c1c; } .btn-secondary{background:#4b5563;}
        input, select, textarea { padding:.4rem .6rem; border:1px solid #d1d5db; border-radius:.375rem; width:100%; margin-bottom:.6rem; font: inherit;}
        label { font-size:.8rem; font-weight:600; display:block; margin-bottom:.15rem; }
        .flash { padding:.6rem 1rem; border-radius:.375rem; margin-bottom:1rem; }
        .flash.success { background:#dcfce7; color:#166534; } .flash.error { background:#fee2e2; color:#991b1b; }
        .badge { padding:.15rem .5rem; border-radius:9999px; font-size:.72rem; background:#e5e7eb; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; }
        .muted { color:#6b7280; font-size:.8rem; }
        form.inline { display:inline; }
    </style>
</head>
<body>
<header>
    <div>
        <strong style="margin-right:1.5rem;">🏢 PropManage</strong>
        @auth
            <nav class="top">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @can('viewAny', App\Models\Property::class)<a href="{{ route('properties.index') }}">Properties</a>@endcan
                @can('viewAny', App\Models\Tenant::class)<a href="{{ route('tenants.index') }}">Tenants</a>@endcan
                @can('viewAny', App\Models\Tenancy::class)<a href="{{ route('tenancies.index') }}">Tenancies</a>@endcan
                @can('viewAny', App\Models\RentInvoice::class)<a href="{{ route('invoices.index') }}">Invoices</a>@endcan
                @can('viewAny', App\Models\Payment::class)<a href="{{ route('payments.index') }}">Payments</a>@endcan
                @can('viewAny', App\Models\RentLedger::class)<a href="{{ route('ledger.index') }}">Ledger</a>@endcan
                @if(auth()->user()->hasPermission('reports.financial'))<a href="{{ route('reports.financial') }}">Reports</a>@endif
                @if(auth()->user()->hasPermission('roles.manage'))<a href="{{ route('admin.roles.index') }}">Roles &amp; Access</a>@endif
            </nav>
        @endauth
    </div>
    <div>
        @auth
            {{ auth()->user()->name }}
            <span class="badge">{{ auth()->user()->assignedRoles->pluck('name')->join(', ') ?: (auth()->user()->is_super_admin ? 'Super Administrator' : 'no role') }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="btn btn-secondary">Logout</button></form>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </div>
</header>
<main>
    @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="flash error"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    {{ $slot }}
</main>
</body>
</html>
