<!DOCTYPE html>
<html>
<head>
    <style>
        .btn { background: #b91c1c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body style="font-family: sans-serif;">
    <h2 style="color: #b91c1c;">Gobierno Regional de Pasco</h2>
    <p>Estimado(a) ciudadano(a),</p>
    <p>Se le comunica que ha recibido una nueva notificación en su casilla electrónica oficial.</p>
    <hr>
    <p><strong>Asunto:</strong> {{ $notificacion->asunto }}</p>
    <p><strong>Fecha de envío:</strong> {{ $notificacion->created_at->format('d/m/Y') }}</p>
    <br>
    <a href="{{ route('casilla.index') }}" class="btn">Ingresar a mi Casilla</a>
    <br><br>
    <p style="font-size: 12px; color: #666;">Este es un mensaje automático, por favor no responda a este correo.</p>
</body>
</html>