# PLANTILLA MVC/PHP DIDÁCTICA PARA CURSO IFCD0210

## Instalación
La plantilla requiere de [composer](https://getcomposer.org) para funcionar.

Instalar la carpeta 'vendor' en el proyecto:

```sh
$ composer install
```

### Dependencias necesarias
* [jQuery] - Validación y Ajax
* [Twig] - Plantillas para generación de vistas en PHP
* [PHPMailer] - Gestión de correos electrónicos 
* [PHP] - Versión 7 y posteriores

### RAMAS
| Rama | Descripción |
| ------ | ------ |
| template | Plantilla MVC básica funcional |
| auth | Registro y login con confirmación de registro mediante email (validación sólo en backend) |
| projects | CRUD de projectos de usuario (nombre, descripción e imagen) y búsqueda |

### DIAGRAMA
Se adjunta en la carpeta raíz del proyecto una diagrama (pdf) del patrón MVC utilizado en la plantilla
