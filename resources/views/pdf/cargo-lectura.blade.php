<h2>Cargo de Notificación Electrónica - GOREPA</h2>
<p><strong>Destinatario:</strong> {{ $usuario->razon_social ?? ($usuario->name . ' ' . $usuario->apellido_paterno) }}</p>
<p><strong>Documento (DNI/RUC):</strong> {{ $usuario->ruc ?? $usuario->dni }}</p>
<hr>
<p><strong>Asunto:</strong> {{ $notificacion->asunto }}</p>
<p><strong>Fecha y Hora de Lectura:</strong> {{ $notificacion->fecha_lectura }}</p>
<p><strong>Dirección IP:</strong> {{ $notificacion->ip_lectura }}</p>
<br>
<p><em>Este documento sirve como constancia de recepción en la Casilla Electrónica del Gobierno Regional de Pasco.</em></p>