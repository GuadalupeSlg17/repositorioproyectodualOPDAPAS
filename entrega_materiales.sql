-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2026 a las 18:00:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `entrega_materiales`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunidades`
--

CREATE TABLE `comunidades` (
  `id_comunidad` int(11) NOT NULL,
  `nombre_comunidad` varchar(70) NOT NULL,
  `id_region` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comunidades`
--

INSERT INTO `comunidades` (`id_comunidad`, `nombre_comunidad`, `id_region`) VALUES
(1, 'COLONIA ADOLFO LÓPEZ MATEOS HUITZIZILAPAN', 1),
(2, 'COLONIA GUADALUPE VICTORIA HUITZIZILAPAN', 1),
(3, 'FLOR DE GALLO HUITZIZILAPAN', 1),
(4, 'LA UNIDAD HUITZIZILAPAN', 1),
(5, 'LAS MESAS HUITZIZILAPAN', 1),
(6, 'SAN AGUSTÍN HUITZIZILAPAN', 1),
(7, 'SAN LORENZO HUITZIZILAPAN', 1),
(8, 'SAN MARTÍN, LAS RAJAS HUITZIZILAPAN', 1),
(9, 'SAN PEDRO HUITZIZILAPAN', 1),
(10, 'SANTA CRUZ HUITZIZILAPAN', 1),
(11, 'ZACAMULPA HUITZIZILAPAN', 1),
(12, 'COLONIA N´DEXI HUITZIZILAPAN', 1),
(13, 'BARRANCA GRANDE TLALMIMILOLPAN', 2),
(14, 'COLONIA ÁLVARO OBREGÓN TLALMIMILOLPAN', 2),
(15, 'LA REFORMA TLALMIMILOLPAN', 2),
(16, 'METATE VIEJO TLALMIMILOLPAN', 2),
(17, 'PUEBLO NUEVO TLALMIMILOLPAN', 2),
(18, 'SANTA CATARINA TLALMIMILOLPAN', 2),
(19, 'SANTA MARÍA TLALMIMILOLPAN', 2),
(20, 'ZACAMULPA TLALMIMILOLPAN', 2),
(21, 'SAN MATEO ATARASQUILLO', 3),
(22, 'SANTA CRUZ CHIGNAHUAPAN', 3),
(23, 'SANTA MARÍA ATARASQUILLO', 3),
(24, 'AMOMOLULCO', 4),
(25, 'CAÑADA DE ALFÉREZ', 4),
(26, 'SALAZAR', 4),
(27, 'SAN JOSÉ EL LLANITO', 4),
(28, 'SAN MIGUEL AMEYALCO', 4),
(29, 'COLONIA AURIS', 5),
(30, 'COLONIA CIDECO', 5),
(31, 'COLONIA CENTRO', 5),
(32, 'COLONIA EL CALVARIO, LA MERCED', 5),
(33, 'COLONIA EL PANTEÓN', 5),
(34, 'COLONIA GUADALUPE LA CIÉNEGA', 5),
(35, 'CONJUNTO URBANO HABITACIONAL VALLE DE ENCINOS', 5),
(36, 'COLONIA LA ESTACIÓN', 5),
(37, 'CONJUNTO URBANO HABITACIONAL HACIENDA LERMA', 5),
(38, 'COLONIA LA MOTA', 5),
(39, 'COLONIA VALLE DE LERMA', 5),
(40, 'COLONIA INFONAVIT', 5),
(41, 'COLONIA ALFREDO DEL MAZO', 6),
(42, 'COLONIA TOMAPA', 6),
(43, 'SAN PEDRO TULTEPEC', 6),
(44, 'SANTA CRUZ TULTEPEC', 6),
(45, 'LA CONCEPCIÓN XOCHICUAUTLA', 7),
(46, 'EL ESPINO PERALTA', 7),
(47, 'SAN FRANCISCO XOCHICUAUTLA', 7),
(48, 'COLONIA AGRÍCOLA ANALCO', 7),
(49, 'SAN NICOLÁS PERALTA', 7),
(50, 'SANTIAGO ANALCO', 7),
(51, 'COLONIA LA BOMBA', 8),
(52, 'CONJUNTO URBANO HABITACIONAL BOSQUES DE LERMA', 8),
(53, 'CONJUNTO URBANO HABITACIONAL PASEOS DE LERMA', 8),
(54, 'CONJUNTO URBANO HABITACIONAL GALAXIAS LERMA', 8),
(55, 'CONJUNTO URBANO HABITACIONAL CEDROS 4000', 8),
(56, 'CONJUNTO URBANO HABITACIONAL EL PORVENIR II', 8),
(57, 'CONJUNTO URBANO HABITACIONAL REAL SANTA CLARA I', 8),
(58, 'COLONIA EMILIANO ZAPATA', 8),
(59, 'CONJUNTO URBANO HABITACIONAL HACIENDA SANTA CLARA', 8),
(60, 'COLONIA ISIDRO FABELA', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

CREATE TABLE `elementos` (
  `id` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('carpeta','pdf','imagen') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL,
  `peso` int(11) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `elementos`
--

INSERT INTO `elementos` (`id`, `folio`, `nombre`, `descripcion`, `tipo`, `parent_id`, `ruta_archivo`, `peso`, `fecha_creacion`) VALUES
(3, 'GENERAL', 'Solicitud_San Nicolas Peralta', NULL, 'carpeta', NULL, NULL, 0, '2026-10-07 00:44:27'),
(4, 'GENERAL', 'Oficio_DG-03092026016', NULL, 'pdf', 3, 'uploads/file_6ac59a9a435b62.89566645.pdf', 16875, '2026-10-07 01:04:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus_solicitud`
--

CREATE TABLE `estatus_solicitud` (
  `id_solicitante` int(11) NOT NULL,
  `folio` varchar(50) DEFAULT NULL,
  `estatus` varchar(20) NOT NULL DEFAULT 'Pendiente',
  `comentario` text DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estatus_solicitud`
--

INSERT INTO `estatus_solicitud` (`id_solicitante`, `folio`, `estatus`, `comentario`, `fecha_actualizacion`) VALUES
(1, 'DG-31072026001', 'Aprobado', 'Su solicitud fue aceptada', '2026-08-21 17:42:07'),
(2, 'DG-31072026002', 'Pendiente', '\r\nEspera de revisión por Dirección General.\r\n\r\n', '2026-08-21 17:41:43'),
(3, 'DG-03082026003', 'No Aprobado', 'Su solicitud fue denegada :(', '2026-08-21 17:42:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_estatus`
--

CREATE TABLE `historial_estatus` (
  `id_historial` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `estatus_anterior` varchar(20) DEFAULT NULL,
  `estatus_nuevo` varchar(20) NOT NULL,
  `comentario` text DEFAULT NULL,
  `usuario` varchar(100) NOT NULL DEFAULT 'sistema',
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_estatus`
--

INSERT INTO `historial_estatus` (`id_historial`, `id_solicitante`, `folio`, `estatus_anterior`, `estatus_nuevo`, `comentario`, `usuario`, `fecha_cambio`) VALUES
(1, 2, '', 'pendiente', 'En Proceso', 'Se esta preparando su material para ser entregado', 'sistema', '2026-08-05 19:00:22'),
(2, 1, '', 'pendiente', 'Aprobado', 'Su solicitud fue aceptada', 'sistema', '2026-08-05 19:18:15'),
(3, 3, '', 'pendiente', 'No Aprobado', 'Su solicitud fue denegada :(', 'sistema', '2026-08-05 19:21:32'),
(4, 2, '', 'En Proceso', 'Pendiente', '\r\nEspera de revisión por Dirección General.\r\n\r\n', 'sistema', '2026-08-05 19:22:24'),
(5, 4, '', 'pendiente', 'En Proceso', 'Su solicitud esta siendo revisada :)', 'sistema', '2026-08-10 19:35:42'),
(6, 0, 'DG-25082026013', 'pendiente', 'Aprobado', '', 'Direccion general', '2026-08-26 01:31:52'),
(7, 0, 'DG-25082026009', 'pendiente', 'En Proceso', '', 'Direccion general', '2026-08-26 01:34:48'),
(8, 0, 'DG-03092026016', 'pendiente', 'En Proceso', 'Por revisar', 'Direccion general', '2026-09-04 00:03:02'),
(9, 0, 'DG-10082026004', 'En Proceso', 'Aprobado', 'Este oficio ya fue autorizado ', 'Direccion general', '2026-10-14 17:12:21'),
(10, 0, 'DG-03092026016', 'En Proceso', 'Aprobado', '', 'Direccion general', '2026-10-14 18:01:46'),
(11, 0, 'DG-02092026015', 'pendiente', 'Aprobado', '', 'Direccion general', '2026-10-19 15:08:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material_solicitado`
--

CREATE TABLE `material_solicitado` (
  `id_material` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `metros` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `material_solicitado`
--

INSERT INTO `material_solicitado` (`id_material`, `id_solicitante`, `folio`, `cantidad`, `descripcion`, `categoria`, `metros`) VALUES
(1, 14, 'DG-02092026014', 6, 'tubos de pvc', 'agua potable', 3.60),
(2, 14, 'DG-02092026014', 3, 'tubos de concreto', 'drenaje', 2.70),
(3, 15, 'DG-02092026015', 6, 'tubos de pvc', 'agua potable', 3.60),
(4, 15, 'DG-02092026015', 3, 'tubos de concreto', 'drenaje', 2.70),
(5, 16, 'DG-03092026016', 5, 'tubos de pvc', 'agua potable', 3.00),
(6, 16, 'DG-03092026016', 3, 'tubos de concreto', 'drenaje', 2.70);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

CREATE TABLE `regiones` (
  `id_region` int(11) NOT NULL,
  `nombre_region` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`id_region`, `nombre_region`) VALUES
(1, 'HUITZIZILAPAN'),
(2, 'TLALMIMILOLPAN'),
(3, 'ATARASQUILLO'),
(4, 'AMEYALCO'),
(5, ' LERMA'),
(6, 'TULTEPEC'),
(7, 'PERALTA-XOCHICUAUTLA-ANALCO'),
(8, ' PARTIDAS - PARQUE INDUSTRIAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitante` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `nombre_solicitante` varchar(100) NOT NULL,
  `puesto` varchar(50) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `calle_beneficiada` varchar(100) NOT NULL,
  `cantidad_mat1` int(11) NOT NULL,
  `descripcion_mat1` varchar(300) DEFAULT NULL,
  `categoria_mat1` varchar(20) NOT NULL,
  `metros_mat1` decimal(10,2) NOT NULL,
  `nombre_region` varchar(60) DEFAULT NULL,
  `nombre_comunidad` varchar(70) DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `estatus` varchar(20) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id_solicitante`, `folio`, `nombre_solicitante`, `puesto`, `telefono`, `calle_beneficiada`, `cantidad_mat1`, `descripcion_mat1`, `categoria_mat1`, `metros_mat1`, `nombre_region`, `nombre_comunidad`, `fecha_solicitud`, `estatus`) VALUES
(14, 'DG-02092026014', 'Lucas', 'Suplente Presidente Copaci', '68304847575', 'Hidalgo', 6, 'tubos de pvc', 'agua potable', 3.60, 'I HUITZIZILAPAN', 'COLONIA ADOLFO LÓPEZ MATEOS HUITZIZILAPAN', '2026-09-03 01:30:09', 'pendiente'),
(15, 'DG-02092026015', 'Lucas', 'Suplente Presidente Copaci', '68304847575', 'Hidalgo', 6, 'tubos de pvc', 'agua potable', 3.60, 'I HUITZIZILAPAN', 'COLONIA ADOLFO LÓPEZ MATEOS HUITZIZILAPAN', '2026-09-03 01:31:11', 'Aprobado'),
(3, 'DG-03082026003', 'Aldo', 'Tercer Delegado/a', '7223567893', 'Universidad', 2, 'tubos de pvc', 'agua potable', 1.20, 'VII PERALTA-XOCHICUAUTLA-ANALCO', 'SAN NICOLÁS PERALTA', '2026-08-03 17:08:20', 'No Aprobado'),
(16, 'DG-03092026016', 'Maria Guadalupe', 'Primer Delegado/a', '3232424236', 'Universidad', 5, 'tubos de pvc', 'agua potable', 3.00, 'VII PERALTA-XOCHICUAUTLA-ANALCO', 'SAN NICOLÁS PERALTA', '2026-09-04 00:00:14', 'Aprobado'),
(4, 'DG-10082026004', 'Axel', 'Segundo Delegado/a Suplente', '34252435378', 'Universidad', 2, 'tubos de pvc', 'agua potable', 1.20, 'VII PERALTA-XOCHICUAUTLA-ANALCO', 'SAN NICOLÁS PERALTA', '2026-08-10 18:07:26', 'Aprobado'),
(5, 'DG-10082026005', 'Aldair', 'Primer Delegado/a Suplente', '768575896', 'Av.Miguel Hidalgo', 6, 'tubos de pvc', 'agua potable', 3.60, 'V LERMA', 'COLONIA EL PANTEÓN', '2026-08-10 18:21:46', 'pendiente'),
(6, 'DG-12082026006', 'Alejandro', 'Primer Delegado/a', '72263456', 'av.universidad', 10, 'tubos de brocales', 'drenaje', 9.00, 'VII PERALTA-XOCHICUAUTLA-ANALCO', 'COLONIA AGRÍCOLA ANALCO', '2026-08-12 17:33:26', 'pendiente'),
(7, 'DG-12082026007', 'Jaziel', 'Primer Delegado/a', '7225008076', 'independencia', 12, 'tubos de pvc', 'agua potable', 7.20, 'V LERMA', 'COLONIA CENTRO', '2026-08-12 19:44:04', 'pendiente'),
(9, 'DG-12082026008', 'Alexa', 'Primer Delegado/a', '759414546455', 'durango', 5, 'tubos de concreto ', 'drenaje', 4.50, 'I HUITZIZILAPAN', 'SAN PEDRO HUITZIZILAPAN', '2026-08-12 20:15:12', 'pendiente'),
(10, 'DG-25082026009', 'Antonio', 'Primer Delegado/a', '73430876901', 'Vicente guerrero', 5, 'tuvos de PVC', 'agua potable', 3.00, 'II TLALMIMILOLPAN', 'COLONIA ÁLVARO OBREGÓN TLALMIMILOLPAN', '2026-08-26 00:28:48', 'En Proceso'),
(11, 'DG-25082026011', 'Sara', 'Presidente copaci', '555466466777', 'Fresnos', 6, 'tuvos de PVC', 'agua potable', 3.60, 'VII PERALTA-XOCHICUAUTLA-ANALCO', 'SANTIAGO ANALCO', '2026-08-26 00:36:58', 'pendiente'),
(12, 'DG-25082026012', 'Simon', 'Tesorero Copaci', '445646545655', 'Fresnos', 4, 'tubos de concentro', 'drenaje', 3.60, 'IV AMEYALCO', 'CAÑADA DE ALFÉREZ', '2026-08-26 00:40:47', 'pendiente'),
(13, 'DG-25082026013', 'Gael', 'Suplente Presidente Copaci', '86826485947', 'Hidalgo', 5, 'tuvos de PVC', 'agua potable', 3.00, 'I HUITZIZILAPAN', 'COLONIA N´DEXI HUITZIZILAPAN', '2026-08-26 00:49:29', 'Aprobado'),
(1, 'DG-31072026001', 'Zoe Natasha', 'Primer Delegado/a', '5650168164', 'durango', 2, 'tubos de pvc', 'agua potable', 1.20, 'I HUITZIZILAPAN', 'SAN PEDRO HUITZIZILAPAN', '2026-07-31 16:48:31', 'Aprobado'),
(2, 'DG-31072026002', 'Gerardo', 'Primer Delegado/a Suplente', '7777777777', 'av.fresnos', 12, 'tubos de pvc', 'agua potable', 7.20, 'I HUITZIZILAPAN', 'SAN PEDRO HUITZIZILAPAN', '2026-07-31 18:40:47', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre_usuario`, `email`, `contraseña`, `rol`) VALUES
(1, 'Guadalupe Slg', 'guadalupz1201@gmail.com', '$2y$12$4fj02s089Ib5oMoufqJGYO.OfR.7WRNyc0l8DKFgOwV8CwEDfJCwq', 'Direccion_General'),
(2, 'Zoe Natasha', 'zoenatgat1793@gmail.com', '$2y$10$LZLHziDR2sJjkP68bSNAgug.w0jWwLnk8.55o98mWV/0.JBZZvWva', 'Infraestructura_Hidraulica');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comunidades`
--
ALTER TABLE `comunidades`
  ADD PRIMARY KEY (`id_comunidad`),
  ADD KEY `id_region` (`id_region`);

--
-- Indices de la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indices de la tabla `estatus_solicitud`
--
ALTER TABLE `estatus_solicitud`
  ADD PRIMARY KEY (`id_solicitante`);

--
-- Indices de la tabla `historial_estatus`
--
ALTER TABLE `historial_estatus`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `idx_solicitante` (`id_solicitante`);

--
-- Indices de la tabla `material_solicitado`
--
ALTER TABLE `material_solicitado`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `id_solicitante` (`id_solicitante`),
  ADD KEY `folio` (`folio`);

--
-- Indices de la tabla `regiones`
--
ALTER TABLE `regiones`
  ADD PRIMARY KEY (`id_region`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`folio`) USING BTREE,
  ADD UNIQUE KEY `fk_solicitante` (`id_solicitante`) USING BTREE;

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comunidades`
--
ALTER TABLE `comunidades`
  MODIFY `id_comunidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `elementos`
--
ALTER TABLE `elementos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estatus_solicitud`
--
ALTER TABLE `estatus_solicitud`
  MODIFY `id_solicitante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_estatus`
--
ALTER TABLE `historial_estatus`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `material_solicitado`
--
ALTER TABLE `material_solicitado`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `regiones`
--
ALTER TABLE `regiones`
  MODIFY `id_region` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `elementos`
--
ALTER TABLE `elementos`
  ADD CONSTRAINT `elementos_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `elementos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
