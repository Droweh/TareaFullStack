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

create table codigo_temporal ( 
    codigo varchar(64) not null,
    correo varchar(320) not null,
    expira datetime not null,
    primary key (codigo),
    foreign key (correo) references usuario(correo)
);

create table tablero (
    id int not null auto_increment,
    nombre varchar(32) not null,
    creador varchar(320) not null,
    fechaCreacion date not null,
    descripcion varchar(500),
    primary key (id),
    foreign key (creador) references usuario(correo)
);

create table colaboraciones (
    tableroId int not null,
    usuario varchar(320) not null,
    primary key (tableroId, usuario),
    foreign key (usuario) references usuario(correo)
);

create table lista (
    titulo varchar(32) not null,
    tableroId int not null,
    primary key (tableroId, titulo),
    foreign key (tableroId) references colaboraciones(tableroId)
);

create table tarea (
    titulo varchar(200) not null,
    lista varchar(32) not null,
    tableroId int not null,
    estado boolean default false,
    fechaInicio date,
    fechaFin date,
    duracion int,
    primary key (titulo, lista),
    foreign key (tableroId, lista) references lista(tableroId, titulo)
);