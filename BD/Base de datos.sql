CREATE DATABASE ERP_ACC;
USE ERP_ACC;

CREATE TABLE roles (
id_rol INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
roles VARCHAR(20) NOT NULL
);

CREATE TABLE departamento(
id_departamento INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
nombre_departamento VARCHAR(50) NOT NULL
);

CREATE TABLE usuario (
id_usuario INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
nombre_usuario VARCHAR(50) NOT NULL,
correo TEXT NOT NULL,
contraseña VARCHAR(25) NOT NULL,
id_rol INT NOT NULL, 
id_departamento INT NOT NULL,
CONSTRAINT fk_usuario_roles FOREIGN KEY (id_rol) REFERENCES Roles(id_rol),
CONSTRAINT fk_usuario_depar FOREIGN KEY (id_departamento) REFERENCES departamento(id_departamento)
);

CREATE TABLE persona (
id_persona INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
id_usuario INT NOT NULL,
nom_persona VARCHAR(50) NOT NULL,
apellido_paterno VARCHAR(20) NOT NULL,
apellido_materno VARCHAR(20) NOT NULL,
curp VARCHAR(18) NOT NULL,
rfc  VARCHAR(13) NOT NULL,
codigo_postal VARCHAR(5),
calle VARCHAR(20),
num_ext TINYINT,
colonia VARCHAR(50),
ciudad VARCHAR(20),
telefono VARCHAR(10) NOT NULL,
sueldo NUMERIC(10,2),
modo_Pago ENUM('Efectivo','Tarjeta','Cheque'),
CONSTRAINT fk_usuario FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE percepciones(
id_percepcion INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
sueldo_base NUMERIC(10,2) NOT NULL,
puntualidad NUMERIC(10,2) NOT NULL,
asistencia NUMERIC(10,2) NOT NULL,
bono NUMERIC(10,2),
vales_despensa NUMERIC(10,2),
compensaciones NUMERIC(10,2),
vacaciones NUMERIC(10,2),
prima_antiguedad NUMERIC(10,2)
);

CREATE TABLE deducciones(
id_deducciones INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
isr NUMERIC(10,2) NOT NULL,
imss NUMERIC(10,2) NOT NULL,
caja_ahorro NUMERIC(10,2) NOT NULL,
prestamos NUMERIC(10,2) NOT NULL,
infonavit NUMERIC(10,2) NOT NULL,
fonacot NUMERIC(10,2) NOT NULL,
cuota_sindical NUMERIC(10,2) NOT NULL
);

CREATE TABLE incapacidad(
id_incapacidad INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
fecha_inicio DATE NOT NULL,
fecha_final DATE NOT NULL,
total_dias INT NOT NULL,
motivo VARCHAR(100) NOT NULL,
estatus ENUM('Activo','Inactivo')
);

CREATE TABLE nomina(
id_nomina INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
fecha_nomina DATE NOT NULL,
periodo_inicio DATE NOT NULL,
periodo_final DATE NOT NULL,
dias_pagados NUMERIC(10,2) NOT NULL,
id_persona INT NOT NULL,
id_incapacidad INT NOT NULL,
id_deducciones INT NOT NULL,
id_percepcion INT NOT NULL,
CONSTRAINT fk_persona FOREIGN KEY (id_persona) REFERENCES persona(id_persona),
CONSTRAINT fk_incapacidad FOREIGN KEY (id_incapacidad) REFERENCES incapacidad(id_incapacidad),
CONSTRAINT fk_deducciones FOREIGN KEY (id_deducciones) REFERENCES deducciones(id_deducciones),
CONSTRAINT fk_percepcion FOREIGN KEY (id_percepcion) REFERENCES percepciones(id_percepcion)
);

