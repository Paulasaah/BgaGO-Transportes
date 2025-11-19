<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva cancelada</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f7f9;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f6f7f9">
    <tr>
        <td align="center" style="padding:0;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="640" style="width:640px;max-width:640px;">
                <tr>
                    <td style="padding:0 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" style="background:#ffffff;border-radius:12px;overflow:hidden;">
                            <tr>
                                <td bgcolor="#ef4444" style="padding:20px;color:#ffffff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td align="left" style="font-size:16px;line-height:1;">
                                                <span style="display:inline-block;vertical-align:middle;color:#ffffff;width:32px;height:32px;"><x-app-logo-icon style="width:32px;height:32px" /></span>
                                                <span style="font-weight:700;margin-left:10px;vertical-align:middle;">{{ config('app.name') }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:24px;font-family:Arial, Helvetica, sans-serif;color:#111;">
                                    <div style="font-size:22px;font-weight:700;margin:0 0 16px;">Tu reserva fue cancelada</div>
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                                        <tr>
                                            <td width="35%" style="padding:6px 0;color:#555;">Código</td>
                                            <td style="padding:6px 0;color:#111;font-weight:700;">{{ $reservation->codigo }}</td>
                                        </tr>
                                        <tr>
                                            <td width="35%" style="padding:6px 0;color:#555;">Motivo</td>
                                            <td style="padding:6px 0;color:#111;">{{ $reservation->motivo_cancelacion ?? 'Sin motivo especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <td width="35%" style="padding:6px 0;color:#555;">Fecha</td>
                                            <td style="padding:6px 0;color:#111;">{{ $reservation->fecha_cancelacion?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:16px;">
                                        <tr>
                                            <td align="left" bgcolor="#1f2937" style="border-radius:8px;">
                                                <a href="{{ rtrim(config('app.url'), '/') }}/catalog/my-reservations" target="_blank" style="display:inline-block;padding:12px 18px;color:#ffffff;text-decoration:none;font-weight:700;font-family:Arial, Helvetica, sans-serif;">Ver mis reservas</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div style="color:#6b7280;font-size:12px;text-align:center;margin:16px 0;font-family:Arial, Helvetica, sans-serif;">Este correo fue enviado por {{ config('app.name') }} • <a href="{{ config('app.url') }}" style="color:#6b7280;text-decoration:none;">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>