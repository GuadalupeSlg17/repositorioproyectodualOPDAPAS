<?php
require 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud</title>
    <link rel="icon" type="image/png" href="favicon.png">

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8d4e8 0%, #a0c9eb 30%, #b7cce7 60%, #cac2f3 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
        }

        h2 {
            text-align: center;
            color: #1b67da;
            margin-bottom: 25px;
            font-size: 28px;
        }

        h3 {
            margin-top: 25px;
            margin-bottom: 15px;
            color: #444;
            border-bottom: 2px solid #2f56ac;
            padding-bottom: 5px;
        }

        h4 {
            color: #356cc0;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .campo-bloque {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="tel"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            border-color: #3a6ddd;
            box-shadow: 0px 0px 5px rgba(34, 101, 177, 0.5);
        }

        .radio-group {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            margin-top: 5px;
        }

        .radio-group input {
            margin-right: 6px;
        }

        button {
            margin-top: 15px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #4870bb;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #3a58b9;
        }

        .btn-eliminar {
            background: #c94c4c;
            margin-top: 5px;
        }

        .btn-eliminar:hover {
            background: #a83232;
        }

        .nav {
            text-align: center;
            margin-top: 15px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .logo {
            width: 200px;
            height: auto;
        }

        .btn {
            display: inline-block;
            background-color: #356cc0;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .material-bloque {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<form action="guardar.php" method="POST">

    <img src="logo.png" alt="Logo" class="logo">

    <h2>Oficio de Solicitud</h2>

    <!-- ============================= -->
    <!-- DATOS DEL SOLICITANTE -->
    <!-- ============================= -->

    <div class="campo-bloque">
        <h3>Datos del solicitante</h3>

        <label>*Nombre del solicitante:</label>

        <input
            type="text"
            name="nombre_solicitante"
            required
        >
    </div>


    <div class="campo-bloque">

        <label>*Cargo:</label>

        <select name="puesto" required>

            <option value="">-- Seleccione el cargo --</option>

            <option>Primer Delegado/a</option>
            <option>Primer Delegado/a Suplente</option>
            <option>Segundo Delegado/a</option>
            <option>Segundo Delegado/a Suplente</option>
            <option>Tercer Delegado/a</option>
            <option>Tercer delegado/a suplente</option>
            <option>Presidente copaci</option>
            <option>Suplente Presidente Copaci</option>
            <option>Secretario Copaci</option>
            <option>Suplente Secretario Copaci</option>
            <option>Tesorero Copaci</option>
            <option>Primer Vocal</option>
            <option>Segundo Vocal</option>
            <option>Otro</option>

        </select>

    </div>


    <div class="campo-bloque">

        <label>*Telefono:</label>

        <input
            type="tel"
            name="telefono"
            required
        >

    </div>


    <div class="campo-bloque">

        <label>*Calle beneficiada:</label>

        <input
            type="text"
            name="calle_beneficiada"
            required
        >

    </div>


    <div class="campo-bloque">

        <label>*Region:</label>

        <select
            id="region"
            name="nombre_region"
            required
        >

            <option value="">
                -- Seleccione una región --
            </option>

            <option value="1">
                HUITZIZILAPAN
            </option>

            <option value="2">
                TLALMIMILOLPAN
            </option>

            <option value="3">
                ATARASQUILLO
            </option>

            <option value="4">
                AMEYALCO
            </option>

            <option value="5">
                LERMA
            </option>

            <option value="6">
                TULTEPEC
            </option>

            <option value="7">
                PERALTA-XOCHICUAUTLA-ANALCO
            </option>

            <option value="8">
                PARTIDAS - PARQUE INDUSTRIAL
            </option>

        </select>

    </div>


    <div class="campo-bloque">

        <label>*Comunidad:</label>

        <select id="comunidad" name="comunidad" required>

            <option value="">-- Seleccione una comunidad --</option>

        </select>

    </div>

    <h3>Material Solicitado</h3>

    <div id="contenedorMateriales">

        <!-- MATERIAL 1 -->

        <div
            class="material-bloque"
            data-indice="0"
        >

            <h4>Material 1</h4>


            <div class="campo-bloque">

                <label>*Cantidad:</label>

                <input
                    type="number"
                    class="cantidad"
                    name="materiales[0][cantidad]"
                    min="1"
                    required
                >

            </div>


            <div class="campo-bloque">

                <label>*Descripción:</label>

                <input
                    type="text"
                    class="descripcion"
                    name="materiales[0][descripcion]"
                    required
                >

            </div>


            <div class="campo-bloque">

                <label>*Categoría:</label>

                <div>

                    <div class="radio-group">

                        <input
                            type="radio"
                            name="materiales[0][categoria]"
                            value="drenaje"
                            required
                        >

                        <label style="font-weight:normal;">
                            Drenaje
                        </label>

                    </div>


                    <div class="radio-group">

                        <input
                            type="radio"
                            name="materiales[0][categoria]"
                            value="agua potable"
                        >

                        <label style="font-weight:normal;">
                            Agua potable
                        </label>

                    </div>

                </div>

            </div>


            <div class="campo-bloque">

                <label>*Mts:</label>

                <input
                    type="number"
                    class="metros"
                    name="materiales[0][metros]"
                    step="0.01"
                    readonly
                    required
                >

            </div>

        </div>

    </div>


    <!-- BOTÓN AGREGAR MATERIAL -->

    <button
        type="button"
        onclick="agregarMaterial()"
    >
        + Agregar material
    </button>


    <!-- BOTÓN GUARDAR -->

    <button type="submit">
        Guardar solicitud
    </button>


    <!-- REGRESAR -->

    <div class="nav">

        <a
            href="index.php"
            class="btn"
        >
            Regresar al inicio
        </a>

    </div>

</form>


<script>

/* =====================================================
   CARGAR COMUNIDADES SEGÚN LA REGIÓN
   ===================================================== */

document.addEventListener("DOMContentLoaded", function() {

    const selectRegion =
        document.getElementById("region");

    const selectComunidad =
        document.getElementById("comunidad");


    selectRegion.addEventListener(
        "change",
        function() {

            const idRegion = this.value;


            if (idRegion === "") {

                selectComunidad.innerHTML =
                    '<option value="">-- Seleccione una comunidad --</option>';

                return;
            }


            selectComunidad.innerHTML =
                '<option value="">Cargando...</option>';


            fetch(
                "obtener_comunidades.php?id_region=" +
                encodeURIComponent(idRegion)
            )

            .then(function(response) {

                if (!response.ok) {

                    throw new Error(
                        "Error HTTP: " + response.status
                    );

                }

                return response.json();

            })

            .then(function(data) {

                selectComunidad.innerHTML =
                    '<option value="">-- Seleccione una comunidad --</option>';


                if (data.length === 0) {

                    selectComunidad.innerHTML =
                        '<option value="">No hay comunidades</option>';

                    return;

                }


                data.forEach(function(com) {

                    const option =
                        document.createElement("option");


                    option.value =
                        com.nombre_comunidad;


                    option.textContent =
                        com.nombre_comunidad;


                    selectComunidad.appendChild(option);

                });

            })

            .catch(function(error) {

                console.error(
                    "Error:",
                    error
                );


                selectComunidad.innerHTML =
                    '<option value="">Error al cargar</option>';

            });

        }
    );

});



/*
   Material 1 = índice 0
   Material 2 = índice 1
   Material 3 = índice 2
   Material 4 = índice 3
*/

let siguienteIndice = 1;

//Funcion para calcular metros.

function calcularMetrosMaterial(material) {

    const cantidad =
        material.querySelector(".cantidad");


    const metros =
        material.querySelector(".metros");


    const categoria =
        material.querySelector(
            'input[type="radio"]:checked'
        );


    const cantidadValor =
        parseFloat(cantidad.value) || 0;


    if (
        categoria &&
        cantidadValor > 0
    ) {

        if (
            categoria.value === "drenaje"
        ) {

            metros.value =
                (cantidadValor * 0.9).toFixed(2);

        }

        else if (
            categoria.value === "agua potable"
        ) {

            metros.value =
                (cantidadValor *6).toFixed(2);

        }

    }

    else {

        metros.value = "";

    }

}


/* 
   ACTIVAR CÁLCULO DEL PRIMER MATERIAL
 */

function activarCalculo(material) {

    const cantidad =
        material.querySelector(".cantidad");


    const categorias =
        material.querySelectorAll(
            'input[type="radio"]'
        );


    cantidad.addEventListener(
        "input",
        function() {

            calcularMetrosMaterial(
                material
            );

        }
    );


    categorias.forEach(
        function(categoria) {

            categoria.addEventListener(
                "change",
                function() {

                    calcularMetrosMaterial(
                        material
                    );

                }
            );

        }
    );

}


/* =====================================================
   ACTIVAR EL PRIMER MATERIAL
   ===================================================== */

const primerMaterial =
    document.querySelector(
        ".material-bloque"
    );


activarCalculo(
    primerMaterial
);


/* =====================================================
   AGREGAR MATERIAL
   ===================================================== */

function agregarMaterial() {

    const contenedor =
        document.getElementById(
            "contenedorMateriales"
        );


    /*
       Guardamos el índice actual.

       Ejemplo:

       Primer material = 0
       Segundo material = 1
       Tercer material = 2
    */

    const indice =
        siguienteIndice;


    siguienteIndice++;


    /*
       Número que verá el usuario
    */

    const numeroVisible =
        document.querySelectorAll(
            ".material-bloque"
        ).length + 1;


    const nuevoMaterial =
        document.createElement("div");


    nuevoMaterial.className =
        "material-bloque";


    nuevoMaterial.dataset.indice =
        indice;


    nuevoMaterial.innerHTML = `

        <h4>
            Material ${numeroVisible}
        </h4>


        <div class="campo-bloque">

            <label>*Cantidad:</label>

            <input
                type="number"
                class="cantidad"
                name="materiales[${indice}][cantidad]"
                min="1"
                required
            >

        </div>


        <div class="campo-bloque">

            <label>*Descripción:</label>

            <input
                type="text"
                class="descripcion"
                name="materiales[${indice}][descripcion]"
                required
            >

        </div>


        <div class="campo-bloque">

            <label>*Categoría:</label>

            <div>

                <div class="radio-group">

                    <input
                        type="radio"
                        name="materiales[${indice}][categoria]"
                        value="drenaje"
                        required
                    >

                    <label style="font-weight:normal;">
                        Drenaje
                    </label>

                </div>


                <div class="radio-group">

                    <input
                        type="radio"
                        name="materiales[${indice}][categoria]"
                        value="agua potable"
                    >

                    <label style="font-weight:normal;">
                        Agua potable
                    </label>

                </div>

            </div>

        </div>


        <div class="campo-bloque">

            <label>*Mts:</label>

            <input
                type="number"
                class="metros"
                name="materiales[${indice}][metros]"
                step="0.01"
                readonly
                required
            >

        </div>


        <button
            type="button"
            class="btn-eliminar"
            onclick="eliminarMaterial(this)"
        >
            Eliminar material
        </button>

    `;


    contenedor.appendChild(
        nuevoMaterial
    );


    /*
       Activamos el cálculo
       para este nuevo material.
    */

    activarCalculo(
        nuevoMaterial
    );

}



function eliminarMaterial(boton) {

    const material =
        boton.closest(
            ".material-bloque"
        );


    if (material) {

        material.remove();

    }

}

</script>

</body>
</html>