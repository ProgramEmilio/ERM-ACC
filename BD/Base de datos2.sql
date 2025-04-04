create database ACC;
use ACC;

-- drop database ACC;
-- drop table Usuario;

/*
USUARIO
CLIENTE
COMPRADOR
PROVEEDOR
PRODUCCION
VENDEDOR
DISTRIBUIDOR
ADMINISTRADOR
*/

-- COMPRA PRODUCCION DISTRIBUCION
CREATE TABLE roles (
id_rol INT NOT NULL PRIMARY KEY,
roles VARCHAR(20) NOT NULL
);

CREATE TABLE usuario (
id_usuario INT NOT NULL PRIMARY KEY,
nombre_usuario VARCHAR(50) NOT NULL,
correo TEXT NOT NULL,
contraseña VARCHAR(25) NOT NULL,
id_rol INT NOT NULL, 
CONSTRAINT fk_usuario_roles FOREIGN KEY (id_rol) REFERENCES Roles(id_rol)
);

CREATE TABLE persona (
id_persona INT NOT NULL PRIMARY KEY,
id_usuario INT NOT NULL,
nom_persona VARCHAR(50) NOT NULL,
apellido_paterno VARCHAR(20) NOT NULL,
apellido_materno VARCHAR(20) NOT NULL,
rfc  VARCHAR(13) NOT NULL,
codigo_postal VARCHAR(5),
calle VARCHAR(20),
num_int TINYINT,
num_ext TINYINT,
colonia VARCHAR(50),
ciudad VARCHAR(20),
telefono VARCHAR(10) NOT NULL,
CONSTRAINT fk_usuario FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);


-- Inventario
-- decimal
-- Insercion preedefinida

create table articulos(
id_articulo INT NOT NULL PRIMARY KEY,
nombre_articulo VARCHAR(70) NOT NULL,
descripcion TEXT NOT NULL,
categoria ENUM('Insumo', 'Producto') NOT NULL, -- Producto - Insumo
precio NUMERIC(16,2),
costo NUMERIC(16,2),
existencias INT NOT NULL DEFAULT 0,
imagen VARCHAR(255),
fecha_registro DATETIME NOT NULL
);

-- COMPRA
-- MATERIA PRIMA
create table solicitud_compra(
id_solicitud INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
id_comprador_usuario INT NOT NULL, -- referencia a la tabla usuario agregar constrain
id_proveedor_usuario INT, -- referencia a la tabla usuario
descripcion TEXT NOT NULL,
total NUMERIC(16,2), -- sumatoria de todos los detalles
fecha_registro DATETIME NOT NULL,
estatus ENUM('generada', 'cotizada', 'comprado') DEFAULT 'generada' NOT NULL,
CONSTRAINT fk_id_comprador_usuario FOREIGN KEY (id_comprador_usuario) REFERENCES usuario(id_usuario),
CONSTRAINT fk_id_proveedor_usuario FOREIGN KEY (id_proveedor_usuario) REFERENCES usuario(id_usuario)
);

create table solicitud_compra_detalle(
id_solicitud_detalle INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
id_solicitud INT NOT NULL, -- referencia a la tabla solicitud_compra agregar constrain
id_articulo INT NOT NULL,
cantidad INT NOT NULL,
subtotal NUMERIC(16,2), -- qutar posiblemente
total NUMERIC(16,2), -- Cantidad por precio unitario
fecha_registro DATETIME NOT NULL,
CONSTRAINT fk_id_solicitud FOREIGN KEY (id_solicitud) REFERENCES solicitud_compra(id_solicitud),
CONSTRAINT fk_id_solicitud_articulo FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo)
);


-- PRODUCCIÓN

-- PRODUCTO TERMINADO
create table pedido(
id_pedido VARCHAR(15) NOT NULL PRIMARY KEY,
id_cliente INT NOT NULL,
estatus ENUM('Generado', 'En preparacion','A enviar','En distribucion', 'En camino','Entregado','Devolución','Recolectar pedido','Recolectar en almacen') DEFAULT 'Generado',
fecha_registro DATETIME NOT NULL,
CONSTRAINT fk_id_cliente_pedi FOREIGN KEY (id_cliente) REFERENCES persona(id_persona)
);

ALTER TABLE pedido ADD COLUMN id_distribuidor INT;
ALTER TABLE pedido ADD CONSTRAINT fk_id_distribuidor_pedi FOREIGN KEY (id_distribuidor) REFERENCES persona(id_persona);


create table producto(
id_producto INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
id_pedido VARCHAR(15),
id_articulo INT NOT NULL,
id_cliente INT,
id_productor INT,
nombre_producto VARCHAR(70) NOT NULL,
cantidad INT NOT NULL,
personalizacion ENUM('icono', 'imagen', 'texto') NOT NULL,
fecha DATETIME NOT NULL,
CONSTRAINT fk_id_cliente_producto FOREIGN KEY (id_cliente) REFERENCES persona(id_persona),
CONSTRAINT fk_id_pedido FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
CONSTRAINT fk_id_articulo_C FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo)
);


create table producto_consumibles(
id_producto_consumibles INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
descripcion TEXT NOT NULL,
id_articulo INT NOT NULL,
id_producto_t INT NOT NULL,
CONSTRAINT fk_id_articulo FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo),
CONSTRAINT fk_id_producto_t FOREIGN KEY (id_producto_t) REFERENCES producto(id_producto)
);

-- DISTRIBUCIÓN

create table pedido_bitacora(
id_pedido_bitacora INT NOT NULL PRIMARY KEY,
id_pedido VARCHAR(15) NOT NULL,
id_usuario INT NOT NULL,
estatus_pedido TEXT NOT NULL,
fecha_registro DATETIME NOT NULL,
CONSTRAINT fk_id_pedido_dis FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
CONSTRAINT fk_id_persona_dis FOREIGN KEY (id_usuario) REFERENCES persona(id_persona)
);

create table comentarios_cliente(
id_comentario INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
id_producto INT NOT NULL,
calificacion INT NOT NULL,
comentario TEXT NOT NULL,
CONSTRAINT fk_id_produc_satis FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);
-- ALTER TABLE Pedido ADD fecha_entrega DATETIME NOT NULL;

INSERT INTO roles VALUES
(1,'Administrador'),
(2,'Cliente'),
(3,'Proveedor'),
(4,'Comprador'),
(5,'Vendedor'),
(6,'Producción'),
(7,'Distribuidor'),
(8,'Responsable stock');


INSERT INTO usuario (id_usuario, nombre_usuario, correo, contraseña, id_rol) VALUES
(1, 'admin', 'admin@ACC.com', '12', 1),
(2, 'cliente1', 'cliente@ACC.com', '12', 2),
(3, 'proveedor1', 'proveedor@ACC.com', '12', 3),
(4, 'comprador1', 'comprador@ACC.com', '12', 4),
(5, 'vendedor1', 'vendedor@ACC.com', '12', 5),
(6, 'produccion1', 'produccion@ACC.com', '12', 6),
(7, 'distribuidor1', 'distribuidor@ACC.com', '12', 7),
(8, 'stock1', 'stock@ACC.com', '12', 8);

INSERT INTO persona (id_persona, id_usuario, nom_persona, apellido_paterno, apellido_materno, rfc, codigo_postal, calle, num_int, num_ext, colonia, ciudad, telefono) VALUES
(1, 1, 'Juan', 'Perez', 'Gomez', 'RFC123456789', '12345', 'Av. Principal', 10, 20, 'Centro', 'Ciudad A', '1234567890'),
(2, 2, 'Maria', 'Lopez', 'Diaz', 'RFC234567890', '54321', 'Calle Secundaria', 5, 15, 'Norte', 'Ciudad B', '0987654321'),
(3, 3, 'Carlos', 'Hernandez', 'Martinez', 'RFC345678901', '67890', 'Calle 3', 8, 30, 'Sur', 'Ciudad C', '1122334455'),
(4, 4, 'Ana', 'Ramirez', 'Torres', 'RFC456789012', '45678', 'Calle 4', 2, 12, 'Este', 'Ciudad D', '2233445566'),
(5, 5, 'Pedro', 'Sanchez', 'Mendoza', 'RFC567890123', '98765', 'Calle 5', 6, 25, 'Oeste', 'Ciudad E', '3344556677'),
(6, 6, 'Luisa', 'Guzman', 'Flores', 'RFC678901234', '11223', 'Calle 6', 9, 18, 'Centro', 'Ciudad F', '4455667788'),
(7, 7, 'Roberto', 'Castro', 'Vega', 'RFC789012345', '33445', 'Calle 7', 4, 22, 'Norte', 'Ciudad G', '5566778899'),
(8, 8, 'Elena', 'Morales', 'Rios', 'RFC890123456', '55667', 'Calle 8', 3, 14, 'Sur', 'Ciudad H', '6677889900');

INSERT INTO articulos (id_articulo, nombre_articulo, descripcion, categoria, precio, costo, existencias, imagen, fecha_registro) VALUES
-- Productos
(1, 'Playera Personalizada', 'Playera personalizada con diseño a elección.', 'Producto', 250.00, 120.00, 50, 'playera2.png', NOW()),
(2, 'Termo Personalizado', 'Termo de acero inoxidable con grabado personalizado.', 'Producto', 300.00, 150.00, 30, 'termo3.png', NOW()),
(3, 'Agenda Personalizada', 'Agenda con portada personalizada.', 'Producto', 200.00, 100.00, 40, 'agenda1.png', NOW()),

-- Insumos
(4, 'Rótulo', 'Rótulo impreso en vinil adhesivo.', 'Insumo', 100.00, 50.00, 20, NULL, NOW()),
(5, 'Cajas de cartón', 'Cajas de cartón para embalaje.', 'Insumo', 15.00, 7.00, 100, NULL, NOW()),
(6, 'Playera', 'Playera en blanco para sublimación.', 'Insumo', 80.00, 40.00, 60, NULL, NOW()),
(7, 'Termo', 'Termo en acero inoxidable sin grabado.', 'Insumo', 200.00, 100.00, 25, NULL, NOW()),
(8, 'Agenda', 'Agenda en blanco lista para personalización.', 'Insumo', 150.00, 75.00, 30, NULL, NOW());

/*
INSERT INTO producto(nombre_producto, descripcion, categoria, precio_unitario, impuestos, cantidad, personalizacion, fecha, imagen) VALUES
('Playera con logo','Playera con logo personalizada tela de algodon','Playera', 250, 50, 2, 'icono', NOW(),'playera2.png'),
('Agenda ejecutiva', 'Agenda de cuero con nombre grabado', 'Agenda', 180, 36, 3, 'texto', NOW(), 'agenda1.png'),  
('Termo metálico', 'Termo de acero inoxidable con diseño grabado', 'Termo', 300, 60, 2, 'imagen', NOW(), 'termo3.png');
*/




