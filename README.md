# PLANTILLA MVC/PHP DIDÁCTICA PARA CURSO IFCD0210

## Instalación
La plantilla requiere de [composer](https://getcomposer.org) para funcionar.

Instalar la carpeta 'vendor' en el proyecto:

```sh
$ composer install
```

### Dependencias necesarias
* -FRONTEND-
* [jQuery] - Todo el desarrollo JS
* [Bootstrap] - Maquetación (responsive)
* [jQuery Validation] - Validación formularios
* [Font-awesome] - Iconos
* [Masonry] - Maquetación de galería de imágenes
* [Photoswipe] - Galería de imágenes
* [Semantic-UI] - Búsqueda de proyectos
* [Polygonizr] - Animación con vectores (formularios de login y registro)
* [Bs-custom-file-input] - Carga de imagen en input[type=file] 
* -BACKEND-
* [Twig] - Plantillas para generación de vistas en PHP
* [PHPMailer] - Gestión de correos electrónicos
* [Verot] - Gestión de subida de imágenes
* [PHP] - Versión 7 y posteriores

### RAMAS
| Rama | Descripción |
| ------ | ------ |
| template | Plantilla MVC básica funcional |
| projectsAjax | REGISTRO / LOGIN / CRUD de projectos de usuario (título, descripción e imagen) y búsqueda |

### DIAGRAMA
Se adjunta en la carpeta raíz del proyecto una diagrama (pdf) del patrón MVC utilizado en la plantilla
