-- Crear tablas para la base de datos

create table usuario (
    nombre varchar(32) not null,
    apellido varchar(32) not null,
    correo varchar(320) not null,
    contraseña varchar(100) not null,
    primary key (correo)
);

create table sesion (
    token varchar(64) not null,
    correo varchar(32) not null,
    fecha datetime not null, 
    primary key (token),
    foreign key (correo) references usuario(correo)
);