<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Tarea Grupal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ==================== VARIABLES GLOBALES ==================== */
        :root {
            --gradient-light: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-dark: linear-gradient(135deg, #4c63d2 0%, #5a3d8b 100%);
            --gradient-accent: linear-gradient(135deg, #a8e6cf 0%, #88d8c0 100%);
            --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.08);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ==================== RESET Y TIPOGRAFÍA ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ==================== LAYOUT PRINCIPAL ==================== */
        .main-content {
            background: var(--gradient-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        /* ==================== HEADER DE PÁGINA ==================== */
        .page-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 0;
            z-index: 10;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* ==================== CONTENEDOR DEL FORMULARIO ==================== */
        .form-container {
            background: var(--gradient-dark);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ==================== GRUPOS DE FORMULARIO ==================== */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-group:last-of-type {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            letter-spacing: 0.3px;
        }

        /* ==================== INPUTS Y TEXTAREA ==================== */
        .form-group input[type="text"],
        .form-group textarea,
        .form-group input[type="datetime-local"] {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
            transition: var(--transition);
            outline: none;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: rgba(168, 230, 207, 0.6);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(168, 230, 207, 0.2);
            transform: translateY(-2px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.5;
        }

        /* ==================== INPUT DATETIME PERSONALIZADO ==================== */
        .form-group input[type="datetime-local"] {
            color-scheme: dark;
            cursor: pointer;
        }

        .form-group input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        /* ==================== SELECTOR DE USUARIOS ==================== */
        .user-select {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 1.25rem;
            max-height: 180px;
            overflow-y: auto;
            backdrop-filter: blur(10px);
        }

        .user-select::-webkit-scrollbar {
            width: 6px;
        }

        .user-select::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .user-select::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .user-select::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .user-select div {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-select div:last-child {
            margin-bottom: 0;
        }

        .user-select input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #a8e6cf;
            cursor: pointer;
        }

        .user-select label {
            color: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            font-weight: 500;
            margin: 0;
            font-size: 0.95rem;
        }

        /* ==================== BOTÓN CREAR ==================== */
        .create-btn {
            background: var(--gradient-accent);
            color: #2d3748;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            margin-top: 2rem;
            box-shadow: var(--shadow-soft);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .create-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #96d9c4 0%, #7bc5b4 100%);
        }

        .create-btn:active {
            transform: translateY(-1px);
        }

        /* ==================== RESPONSIVE DESIGN ==================== */
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .page-header {
                padding: 1rem 0;
            }
            
            .page-header h1 {
                font-size: 1.7rem;
            }
            
            .form-container {
                padding: 2rem 1.5rem;
                margin-top: 1rem;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
            
            .form-group input,
            .form-group textarea {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }
            
            .create-btn {
                padding: 0.875rem 2rem;
                font-size: 1rem;
            }
            
            .user-select {
                max-height: 140px;
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .form-container {
                padding: 1.5rem 1rem;
                border-radius: 12px;
            }
            
            .form-group label {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1>Nueva Tarea Colaborativa</h1>
        </div>

        <div class="form-container">
            <form method="POST" action="../backend/comunidad/crear_grupal.php">
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Ingresa el título de la tarea" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe los detalles de la tarea colaborativa" required></textarea>
                </div>

                <div class="form-group">
                    <label for="fecha_limite">Fecha Límite:</label>
                    <input type="datetime-local" id="fecha_limite" name="fecha_limite" required>
                </div>

                <div class="form-group">
                    <label>Asignar a miembros:</label>
                    <div class="user-select">
                        <!-- Ejemplo de usuarios - esto debería generarse dinámicamente desde PHP -->
                        <div>
                            <input type="checkbox" id="user1" name="usuarios[]" value="1">
                            <label for="user1">Juan Pérez</label>
                        </div>
                        <div>
                            <input type="checkbox" id="user2" name="usuarios[]" value="2">
                            <label for="user2">María González</label>
                        </div>
                        <div>
                            <input type="checkbox" id="user3" name="usuarios[]" value="3">
                            <label for="user3">Carlos Rodríguez</label>
                        </div>
                        <div>
                            <input type="checkbox" id="user4" name="usuarios[]" value="4">
                            <label for="user4">Ana Martínez</label>
                        </div>
                        <div>
                            <input type="checkbox" id="user5" name="usuarios[]" value="5">
                            <label for="user5">Luis Hernández</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="create-btn">
                    <span>+</span>
                    Crear Tarea Grupal
                </button>
            </form>
        </div>
    </div>
</body>
</html>