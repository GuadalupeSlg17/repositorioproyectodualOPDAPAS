<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Registro - OPDAPAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            user-select: none;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            background: linear-gradient(100deg, #ADD8E6  100%);
        }
        canvas {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .opdapas-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2), 0 0 20px rgba(168, 85, 247, 0.3);
            width: 100%;
            max-width: 420px;
            z-index: 10;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }
        .opdapas-title {
            font-family: 'Playfair Display', serif;
        }
        .input-field {
            background-color: #e2e8f0;
            border: 1px solid transparent;
            border-radius: 20px;
            padding: 10px 18px;
            font-size: 14px;
            color: #334155;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-field:focus {
            background-color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
        }
        .btn-opdapas {
            background: #7095e4c2;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
        }
        .btn-opdapas:hover {
            background: #0285c7c2;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="relative flex items-center justify-center min-h-screen">

    <!-- Canvas con la misma animación de gotas -->
    <canvas id="waterCanvas"></canvas>

    <!-- Tarjeta de Registro -->
    <div class="opdapas-card p-6 m-4 relative pointer-events-auto">
        <div class="logo-container text-center mb-3">
            <img src="desktop-logo (1).png" alt="Logo OPDAPAS">
        </div>

        <h2 class="opdapas-title text-center text-xl font-bold text-gray-900 mb-3">Crear Cuenta</h2>

        <form action="procesar_registro.php" method="POST" class="space-y-3">
            
            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1">Nombre completo</label>
                <input type="text" name="nombre" placeholder="Tu nombre" class="input-field w-full" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1">Correo electrónico</label>
                <input type="email" name="correo" placeholder="xxxxxx@gmail.com" class="input-field w-full" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1">Contraseña</label>
                <input type="password" name="password" placeholder="Crea una contraseña" class="input-field w-full" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-800 mb-1">Rol en el sistema</label>
                <select name="rol" class="input-field w-full cursor-pointer">
                    <option value="Direccion_General">Dirección General</option>
                    <option value="Infraestructura_Hidraulica">Infraestructura Hidráulica</option>
                </select>
            </div>

            <button type="submit" name="btn_registrar" class="btn-opdapas w-full py-2.5 text-white text-sm font-bold mt-2">
                Registrarse
            </button>

            <div class="text-center pt-2">
                <a href="inicio_sesion.html" class="text-xs text-sky-600 font-bold underline hover:text-sky-700 transition">
                    ¿Ya tienes una cuenta? Inicia sesión
                </a>
            </div>
        </form>
    </div>

    <script>
        const canvas = document.getElementById('waterCanvas');
        const ctx = canvas.getContext('2d');

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            initDroplets();
        });

        const mouse = { x: -1000, y: -1000, radius: 100 };
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });
        window.addEventListener('mouseleave', () => {
            mouse.x = -1000;
            mouse.y = -1000;
        });

        
    </script>
</body>
</html>