-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 06-05-2025 a las 00:21:45
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
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `tipo_movimiento` enum('AGREGADO','EDITADO','ELIMINADO') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial`
--

INSERT INTO `historial` (`id`, `producto_id`, `usuario`, `tipo_movimiento`, `descripcion`, `fecha`) VALUES
(1, 1, 'admin', 'AGREGADO', 'Producto agregado: camisa', '2025-05-02 20:22:04'),
(2, 1, 'admin', 'EDITADO', 'Producto editado: camisa', '2025-05-02 20:22:40'),
(3, 1, 'admin', 'EDITADO', 'Producto editado: camisa', '2025-05-02 20:23:21'),
(4, 1, 'admin', 'EDITADO', 'Producto editado: camisa', '2025-05-02 20:23:38'),
(5, 1, '', 'EDITADO', 'Producto editado: saco', '2025-05-05 21:04:10'),
(6, 1, '', 'EDITADO', 'Producto editado: saco', '2025-05-05 22:10:38'),
(7, 2, '', 'EDITADO', 'Producto editado: Camisa deportiva', '2025-05-05 22:10:45'),
(8, 1, '', 'ELIMINADO', 'Producto eliminado: saco', '2025-05-05 22:11:00'),
(9, 12, '', 'AGREGADO', 'Producto agregado: pantalon', '2025-05-05 22:13:36'),
(10, 12, '', 'ELIMINADO', 'Producto eliminado: pantalon', '2025-05-05 22:13:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `categoria`, `fecha_registro`) VALUES
(2, 'Camisa deportiva', 'Camisa de algodón transpirable', 45000.00, 0, 'Ropa', '2025-05-05 14:52:06'),
(3, 'Laptop Genérica', 'Laptop 15.6\" con Windows', 2500000.00, 10, 'Tecnología', '2025-05-05 14:52:06'),
(4, 'Shampoo Herbal', 'Shampoo natural para todo tipo de cabello', 12000.00, 50, 'Cuidado personal', '2025-05-05 14:52:06'),
(5, 'Cámara de Seguridad', 'Cámara Wi-Fi visión nocturna', 180000.00, 15, 'Seguridad', '2025-05-05 14:52:06'),
(6, 'Zapatillas Urbanas', 'Zapatillas negras casuales unisex', 85000.00, 0, 'Calzado', '2025-05-05 14:52:06'),
(7, 'USB 32GB', 'Memoria USB 3.0 de 32GB', 22000.00, 100, 'Accesorios', '2025-05-05 14:52:06'),
(8, 'Silla Ergonómica', 'Silla de oficina reclinable', 350000.00, 5, 'Muebles', '2025-05-05 14:52:06'),
(9, 'Café Molido 500g', 'Café colombiano premium', 25000.00, 30, 'Alimentos', '2025-05-05 14:52:06'),
(10, 'Parlante Bluetooth', 'Mini parlante portátil resistente al agua', 60000.00, 12, 'Tecnología', '2025-05-05 14:52:06'),
(11, 'Cuaderno Profesional', 'Cuaderno de 100 hojas cuadriculadas', 7000.00, 80, 'Papelería', '2025-05-05 14:52:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','empleado','supervisor') NOT NULL DEFAULT 'empleado',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `clave`, `rol`, `creado_en`) VALUES
(1, 'Administrador', 'admin', '$2y$10$6gRoW0bkV.y7e0OEuxZtRu7cQInneFecNdbWAv51IzTA8uLEWKH7a', 'admin', '2025-05-05 16:06:43'),
(2, 'Empleado Pedro', 'pedro', '$2y$10$ROID/8kTfp69kGy90fuhAuUfB.qSa/rRnifZDBun5V3czTHxoBKPy', 'empleado', '2025-05-05 16:06:43'),
(3, 'Supervisor Laura', 'laura', '$2y$10$ZEsvc4ilTe.vf3a7qfYwDunB3DrsHwz.Rl8GItDWyXt5WSp/FHH/2', 'supervisor', '2025-05-05 16:06:43'),
(5, 'cristian', 'cristian', '$2y$10$EeVkg829AUPJYs/3kMkrjOFZEJjkTcvtA0VJd7SrayYZjviBOMoq6', 'supervisor', '2025-05-05 17:47:19'),
(6, 'camilo', 'camilo', '$2y$10$tyXqYmhqYOpoa6QhHg3ylOq1WIH5NBy1g9G5bxfc0kYOkpVCFRIAO', 'admin', '2025-05-05 17:48:06'),
(8, '', 'diego', '$2y$10$hHbR9AA.BqbG0dKCGqLDhOxLGBc8yA.OM3v8aZfSY1hKeGI88xSS2', 'admin', '2025-05-05 21:15:59'),
(9, '', 'rivera', '$2y$10$87jhUaLamVUBU.4HD66qPOxMjlq1DbSjxxfCuhbP6P0pTQFkzXPa.', 'admin', '2025-05-05 22:09:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
