<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .card { background: white; max-width: 600px; margin: auto; padding: 30px; border-radius: 8px; border-top: 5px solid #0EA5E9; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn { display: inline-block; background-color: #0EA5E9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 20px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color: #1f2937; margin-top: 0;">Gobierno Regional de Pasco</h2>
        <p>Estimado(a) <strong>{{ $nombreCiudadano }}</strong>,</p>
        
        <p>Se ha depositado un nuevo documento administrativo en su Casilla Electrónica.</p>
        
        <div style="background-color: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <strong>Asunto:</strong> {{ $asuntoNotificacion }}
        </div>

        <p>Para visualizar el contenido y generar su constancia de recepción, por favor ingrese al sistema:</p>
        
        <center>
            <a href="{{ route('login') }}" class="btn">Ingresar a mi Casilla</a>
        </center>
        
        <div class="footer">
            <p>Este es un mensaje automático, por favor no responder.<br>
            Oficina de Tecnologías de la Información - GORE PASCO</p>
        </div>
    </div>
</body>
</html>