<div style="font-family: sans-serif; background-color: #f4f7f6; padding: 40px;">
    <div style="max-width: 500px; margin: auto; background: white; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="background-color: #57C1C7; padding: 30px; text-align: center; color: white;">
            <h2 style="margin: 0;">GORE PASCO</h2>
            <p style="margin: 0; font-size: 12px; opacity: 0.8;">Casilla Electrónica Regional</p>
        </div>
        <div style="padding: 30px; color: #333;">
            <p>Hola <strong>{{ $usuario->name }}</strong>,</p>
            <p>Tu solicitud ha sido aprobada. Aquí tienes tus credenciales de acceso:</p>
            
            <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #edf2f7; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Usuario:</strong> {{ $usuario->dni }}</p>
                <p style="margin: 5px 0;"><strong>Contraseña:</strong> <span style="color: #57C1C7; font-family: monospace;">{{ $password }}</span></p>
            </div>

            <p style="font-size: 11px; color: #718096;">* Te recomendamos cambiar tu contraseña al ingresar por primera vez.</p>
            
            <a href="{{ url('/login') }}" style="display: block; text-align: center; background: #57C1C7; color: white; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold; margin-top: 20px;">
                INGRESAR AL SISTEMA
            </a>
        </div>
    </div>
</div>