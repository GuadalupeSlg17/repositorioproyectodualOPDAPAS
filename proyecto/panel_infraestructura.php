<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Inicio - Sistema de entrega de material</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #f8d4e8 0%, #8fc6f3 30%, #b7cce7 60%, #cac2f3 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.contenedor {
    background: white;
    width: 100%;
    max-width: 1100px;
    padding: 60px 50px;
    border-radius: 24px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    overflow: visible;
}
.logo {
    width: 130px;
    margin-bottom: 25px;
}
h1 {
    color: #1a1a2e;
    font-size: 2.4rem;
    margin-bottom: 12px;
    line-height: 1.3;
    font-weight: 700;
}
p {
    color: #6b7280;
    font-size: 1.15rem;
    margin-bottom: 50px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
.menu-wrapper {
    position: relative;
    width: 100%;
}
.menu {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: flex-start;
    gap: 24px;
    overflow-x: auto;
    padding: 10px 20px 20px 20px;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #c4b5fd transparent;
}
.menu::-webkit-scrollbar {
    height: 8px;
}
.menu::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 10px;
}
.menu::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #a78bfa, #60a5fa);
    border-radius: 10px;
}
.scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #6b7280;
    z-index: 10;
    transition: all 0.2s ease;
}
.scroll-btn:hover {
    background: #f9fafb;
    color: #1a1a2e;
}
.scroll-btn.prev {
    left: 0;
}
.scroll-btn.next {
    right: 0;
}
.boton {
    background: white;
    color: #1a1a2e;
    text-decoration: none;
    padding: 0;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 600;
    transition: all 0.3s ease;
    flex: 0 0 260px;
    width: 260px;
    min-width: 260px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    border: 1px solid #f3f4f6;
    overflow: hidden;
    position: relative;
}
.boton:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.12);
}
.boton:active {
    transform: translateY(-2px);
}
.boton-header {
    padding: 28px 28px 0 28px;
    width: 100%;
}
.boton-icono {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 16px;
}
.boton:nth-child(1) .boton-icono { background: linear-gradient(135deg, #0b8efa, #0b8efa); }
.boton:nth-child(2) .boton-icono { background: linear-gradient(135deg, #34d399, #34d399); }
.boton:nth-child(3) .boton-icono { background: linear-gradient(135deg, #60a5fa, #60a5fa); }
.boton:nth-child(4) .boton-icono { background: linear-gradient(135deg, #fbbf24, #fbbf24); }
.boton:nth-child(5) .boton-icono { background: linear-gradient(135deg, #f9a8d4, #f9a8d4); }
.boton:nth-child(6) .boton-icono { background: linear-gradient(135deg, #a78bfa, #a78bfa); }
.boton:nth-child(7) .boton-icono { background: linear-gradient(135deg, #9cecad, #9cecad); }
.boton:nth-child(8) .boton-icono { background: linear-gradient(135deg, #e416d3, #e416d3); }
.boton:nth-child(9) .boton-icono { background: linear-gradient(135deg, #0d38f8, #0d38f8); }
.boton-titulo {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 6px;
}
.boton-desc {
    font-size: 0.9rem;
    font-weight: 400;
    color: #9ca3af;
    line-height: 1.4;
}
.boton-wave {
    width: 100%;
    height: 100px;
    margin-top: 20px;
    position: relative;
    overflow: hidden;
}
.boton:nth-child(1) .boton-wave { background: linear-gradient(135deg, #0b8efa, #0b8efa); }
.boton:nth-child(2) .boton-wave { background: linear-gradient(135deg, #6495ED, #6495ED); }
.boton:nth-child(3) .boton-wave { background: linear-gradient(135deg, #93c5fd, #60a5fa); }
.boton:nth-child(4) .boton-wave { background: linear-gradient(135deg, #6495ED, #6495ED); }
.boton:nth-child(5) .boton-wave { background: linear-gradient(135deg, #4169E1, #4169E1); }
.boton:nth-child(6) .boton-wave { background: linear-gradient(135deg, #87CEEB, #87CEEB);  }
.boton:nth-child(7) .boton-wave { background: linear-gradient(135deg, #0000FF, #0000FF); }
.boton:nth-child(8) .boton-wave { background: linear-gradient(135deg, #00008B, #00008B); }
.boton:nth-child(9) .boton-wave { background: linear-gradient(135deg, #0d38f8, #0d38f8); }

.boton-wave:after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -20%;
    width: 140%;
    height: 80px;
    background: white;
    border-radius: 50% 50% 0 0;
}
.boton-wave-icon {
    position: absolute;
    bottom: 20px;
    right: 24px;
    width: 48px;
    height: 48px;
    z-index: 2;
}
.boton-wave-icon svg {
    width: 100%;
    height: 100%;
    fill: white;
    opacity: 0.9;
}
    </style>
</head>
<body>

    <div class="contenedor">
        <img src="opdapas_somost.png" alt="Logo" class="logo">
        <h1>Bienvenido(a) al sistema de entrega de material</h1>
        <p>Seleccione la acción que desea realizar.</p>
        
                <a href="ver_solicitudes.php" class="boton">
                    <div class="boton-header">
                        <div class="boton-icono"></div>
                        <div class="boton-titulo">Visualizacion de Solicitudes</div>
                        <div class="boton-desc">Consultar la informacion de las solicitudes</div>
                    </div>
                    <div class="boton-wave">
                        <div class="boton-wave-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </div>
                    </div>
                </a>
                
                <a href="archivos_solicitud.php" class="boton">
                    <div class="boton-header">
                        <div class="boton-icono"></div>
                        <div class="boton-titulo">Archivos de solicitudes</div>
                        <div class="boton-desc">Sube aqui la documentacion de cada solicitud.</div>
                    </div>
                    <div class="boton-wave">
                        <div class="boton-wave-icon">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        </div>
                    </div>
                </a>
                
                
                <a href="ver_registro material.php" class="boton">
                    <div class="boton-header">
                        <div class="boton-icono"></div>
                        <div class="boton-titulo">Visualizacion del Material</div>
                        <div class="boton-desc">Visualizar los materiales solicitados por las comunidades</div>
                    </div>
                    <div class="boton-wave">
                        <div class="boton-wave-icon">
                            <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        </div>
                    </div>
                </a>
                <a href="convenios_oficio_liberado.php" class="boton">
                    <div class="boton-header">
                        <div class="boton-icono"></div>
                        <div class="boton-titulo">Visualización de archivos autorizados</div>
                        <div class="boton-desc">Revisa los convenios y oficios liberados disponibles.</div>
                    </div>
                    <div class="boton-wave">
                        <div class="boton-wave-icon">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        </div>
                    </div>
                </a>

            </div>
            <button class="scroll-btn next" onclick="document.querySelector('.menu').scrollBy({left: 300, behavior: 'smooth'})">&#9654;</button> 
        </div>
    </div>

</body>
</html>