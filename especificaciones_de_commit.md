---

# Especificaciones de Commit

## Estructura general

Cada commit debe seguir el siguiente formato:

```
[ÁREA] [TIPO]
<Descripción breve del cambio>

<Detalles adicionales opcionales>
```

---

## 1. ÁREA

Indica **dónde se realizaron los cambios**.
Los valores válidos son:

| Área                    | Descripción                                                                   |
| ----------------------- | ----------------------------------------------------------------------------- |
| **Frontend**            | Cambios realizados en la interfaz de usuario o lógica del cliente.            |
| **Backend**             | Cambios en la lógica del servidor, base de datos o API.                       |
| **Mixto**               | Cambios que afectan tanto el frontend como el backend.                        |
| **Sistema de Archivos** | Cambios en la estructura del proyecto, configuración, dependencias o scripts. |

---

## 2. TIPO

Indica **qué tipo de cambio** se hizo.
Los valores válidos son:

| Tipo               | Descripción                                                                        |
| ------------------ | ---------------------------------------------------------------------------------- |
| **Implementación** | Adición de nuevas funciones o características.                                     |
| **Desarrollo**     | Cambios grandes o importantes que mejoran o modifican implementaciones existentes. |
| **Arreglo**        | Cambios pequeños destinados a corregir errores o comportamientos inesperados.      |

---

## 3. Descripción breve

Una línea que resuma el propósito principal del cambio.
Debe ser concisa, directa y comenzar con mayúscula.

Ejemplo:

```
Implementación de sistema de Login y Registro.
```

---

## 4. Detalles adicionales (opcional)

Pueden incluir:

* Explicaciones técnicas del cambio.
* Clases, archivos o módulos modificados.
* Razón del cambio (por qué se hizo).
* Notas de compatibilidad o pendientes.

---

## Ejemplos

### Ejemplo 1: Implementación

```
Backend Implementación
Implementación de sistema de Login y Registro.

Se ha creado una clase de perfil con los métodos de registro y login.
```

### Ejemplo 2: Arreglo

```
Frontend Arreglo
Corrección de bug en el formulario de registro.

El botón de enviar no respondía cuando el campo "correo" estaba vacío.
```

### Ejemplo 3: Desarrollo

```
Mixto Desarrollo
Reestructuración del flujo de autenticación.

Se modificó la API de login y la vista principal para integrar JWT.
```

### Ejemplo 4: Sistema de Archivos

```
Sistema de Archivos Implementación
Configuración inicial del entorno del proyecto.

Se agregaron dependencias base y archivos de configuración (.env, .gitignore).
```
