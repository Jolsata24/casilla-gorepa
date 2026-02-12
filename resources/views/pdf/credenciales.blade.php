<!DOCTYPE html>
<html>
<head>
    <title>Credenciales de Acceso - GOREPA</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .titulo { font-size: 24px; font-weight: bold; color: #1a56db; }
        .subtitulo { font-size: 16px; color: #666; }
        .caja-credenciales { 
            border: 2px dashed #1a56db; 
            padding: 20px; 
            background-color: #f0f9ff; 
            margin: 20px 0;
            text-align: center;
        }
        .label { font-size: 14px; color: #666; margin-bottom: 5px; }
        .valor { font-size: 20px; font-weight: bold; color: #000; margin-bottom: 15px; letter-spacing: 1px; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <div class="titulo">GOBIERNO REGIONAL DE PASCO</div>
        <div class="subtitulo">Sistema de Casilla Electrónica</div>
    </div>

    <p>Estimado(a) <strong>{{ $user->name }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}</strong>,</p>

    <p>Su solicitud de acceso ha sido <strong>APROBADA</strong>. A continuación se detallan sus credenciales para acceder al sistema:</p>

    <div class="caja-credenciales">
        <div class="label">USUARIO (DNI)</div>
        <div class="valor">{{ $user->dni }}</div>

        <div class="label">CONTRASEÑA TEMPORAL</div>
        <div class="valor">{{ $password }}</div>
    </div>

    <p><strong>Instrucciones:</strong></p>
    <ul>
        <li>Ingrese al sistema en: <u>{{ url('/') }}</u></li>
        <li>Utilice estas credenciales para su primer acceso.</li>
        <li>Por seguridad, el sistema podría solicitarle cambiar su contraseña.</li>
    </ul>

    <div class="footer">
        Documento generado el {{ $fecha }}.<br>
        Por favor, mantenga estas credenciales en un lugar seguro.
    </div>

</body>
</html>