skydata
=======

## WorkBench 0.1

El workbench es una aplicación PHP que permite generar código por defecto delos distintos recursos que forman el framework web. Con 
el se pueden generar el código básico para páginas, módulos y servicios.

Use:
```
	>workbench  [page|service|module]  "Nombre"
```

Dónde:
  *page|service|module* Indicando uno de éstos, se generará el código de una página, un servicio o un módulo, respectivamente, y en 
  lo directorios correctos dentro del árbol de la aplicación.
  
  *Nombre*  Es el nombre que tomará la clase del recurso que se genera. Ej: `CustomMenu`. Si se genera un módulo con este nombre, se
  creará un directorio dentro de */Modules*
  