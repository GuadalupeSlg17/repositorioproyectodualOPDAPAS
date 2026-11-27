<?php

require 'conexion.php';

$sql = "
    SELECT
        id_material,
        id_solicitante,
        folio,
        cantidad,
        descripcion,
        categoria,
        metros
    FROM material_solicitado
    ORDER BY id_material DESC
";

$resultado = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Registro de materiales
    </title>

    <link
        rel="icon"
        type="image/png"
        href="favicon.png"
    >

    <style>

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #f8d4e8 0%,
                #a0c9eb 30%,
                #b7cce7 60%,
                #cac2f3 100%
            );

            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }


        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }


        h2 {
            color: #333;
            border-bottom: 3px solid #1257b1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }


        .btn-nuevo {
            display: inline-block;
            background-color: #356cc0;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }


        .btn-nuevo:hover {
            background-color: #2b59bb;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }


        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            vertical-align: top;
        }


        th {
            background-color: #356cc0;
            color: white;
        }


        tr:hover {
            background-color: #f1f1f1;
        }


        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin: 4px 0;
        }


        .badge-agua {
            background-color: #e3f2fd;
            color: #0d47a1;
        }


        .badge-drenaje {
            background-color: #efebe9;
            color: #4e342e;
        }


        .badge-ninguno {
            background-color: #f5f5f5;
            color: #9e9e9e;
        }


        .text-empty {
            text-align: center;
            color: #777;
            padding: 30px;
        }


        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }


        /* ===== ESTILOS BOTONES DE ACCIÓN ===== */

        .btn-accion {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
            margin-bottom: 4px;
            transition: opacity 0.2s;
            border: none;
            cursor: pointer;
        }


        .btn-accion:hover {
            opacity: 0.85;
        }


        .btn-editar {
            background-color: #ffc107;
            color: #212529;
        }


        .btn-eliminar {
            background-color: #dc3545;
            color: white;
        }


        /* ===== ESTILO PARA CATEGORÍAS ===== */

        .categoria {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }


        .categoria-agua {
            background-color: #e3f2fd;
            color: #0d47a1;
        }


        .categoria-drenaje {
            background-color: #efebe9;
            color: #4e342e;
        }


        /* ===== BOTÓN REGRESAR ===== */

        .regresar {
            display: inline-block;
            background-color: #356cc0;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }


        .regresar:hover {
            background-color: #2b59bb;
        }

    </style>

</head>


<body>


<div class="container">

    <img
        src="logo.png"
        alt="Logo"
        class="logo"
    >


    <h2>
        Registro de materiales solicitados
    </h2>


    <table>

        <thead>

            <tr>

                <th>
                    Id material
                </th>

                <th>
                    Folio
                </th>

                <th>
                    Cantidad
                </th>

                <th>
                    Descripción
                </th>

                <th>
                    Categoría
                </th>

                <th>
                    Metros
                </th>

                <th>
                    Acciones
                </th>

            </tr>

        </thead>


        <tbody>

        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <?php while ($fila = $resultado->fetch_assoc()): ?>

                <?php

                $categoria = strtolower(
                    trim($fila['categoria'])
                );

                if ($categoria === 'agua potable') {

                    $claseCategoria = 'categoria-agua';

                } elseif ($categoria === 'drenaje') {

                    $claseCategoria = 'categoria-drenaje';

                } else {

                    $claseCategoria = '';

                }

                ?>


                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $fila['id_material']
                        ) ?>
                    </td>


                    <td>
                        <strong>
                            <?= htmlspecialchars(
                                $fila['folio']
                            ) ?>
                        </strong>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $fila['cantidad']
                        ) ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $fila['descripcion']
                        ) ?>
                    </td>


                    <td>

                        <span class="categoria <?= $claseCategoria ?>">

                            <?= htmlspecialchars(
                                $fila['categoria']
                            ) ?>

                        </span>

                    </td>


                    <td>

                        <?= number_format(
                            (float)$fila['metros'],
                            2
                        ) ?>

                        mts

                    </td>


                    <td>

                        <a
                            href="editar_material.php?id=<?= $fila['id_material'] ?>"
                            class="btn-accion btn-editar"
                        >
                            Editar
                        </a>


                        <a
                            href="eliminar_material.php?id=<?= $fila['id_material'] ?>"
                            class="btn-accion btn-eliminar"
                            onclick="
                                return confirm(
                                    '¿Deseas eliminar este material?'
                                );
                            "
                        >
                            Eliminar
                        </a>

                    </td>

                </tr>


            <?php endwhile; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="7"
                    class="text-empty"
                >
                    No hay materiales registrados.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>


    <a
        href="index.php"
        class="regresar"
    >
        Regresar al inicio
    </a>


</div>


</body>

</html>


<?php

$conexion->close();

?>