<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body{margin:0;background:#f6f7f9;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;color:#111}
        .wrap{max-width:640px;margin:0 auto;padding:0 16px}
        .card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08);margin:24px 0}
        .brand{background:#0ea5e9;color:#fff;padding:20px}
        .logo{display:flex;align-items:center;gap:10px}
        .content{padding:24px}
        .title{margin:0 0 12px;font-size:20px}
        .meta{margin:6px 0}
        .cta{display:inline-block;margin-top:16px;background:#1d4ed8;color:#fff;text-decoration:none;padding:10px 14px;border-radius:8px}
        .footer{color:#6b7280;font-size:12px;text-align:center;margin:16px 0}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="brand">
            <div class="logo">
                <span style="width:32px;height:32px;display:inline-block;color:#fff"><x-app-logo-icon style="width:32px;height:32px" /></span>
                <strong>{{ config('app.name') }}</strong>
            </div>
        </div>
        <div class="content">
            <h2 class="title">Has iniciado sesión</h2>
            <p class="meta">Hola {{ $user->name }}, tu cuenta inició sesión correctamente.</p>
            <p class="meta">Fecha: {{ now()->format('Y-m-d H:i') }}</p>
            <a class="cta" href="{{ rtrim(config('app.url'), '/') }}/dashboard" target="_blank">Ir al dashboard</a>
        </div>
    </div>
    <div class="footer">Este correo fue enviado por {{ config('app.name') }} • <a href="{{ config('app.url') }}" style="color:#6b7280">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a></div>
</div>
</body>
</html>