---
name: registro-de-cambios
description: Skill obligatorio para registrar todas las modificaciones realizadas en el proyecto en el archivo CHANGE_MANAGEMENT.csv
---

# Registro de Cambios

Es OBLIGATORIO que cada vez que modifiques, crees o elimines código, registres el cambio en el archivo `CHANGE_MANAGEMENT.csv` ubicado en la raíz del proyecto.

## Instrucciones

1. Abre el archivo `CHANGE_MANAGEMENT.csv`.
2. Añade una nueva línea al final del archivo con el siguiente formato CSV (separado por comas, y usando comillas dobles `""` si el texto interno de una columna contiene comas):
   `YYYY-MM-DD HH:MM,Tipo (Ej. Feature/Bugfix),Elemento (Ej. Vista/Controlador),"Archivos afectados",Descripción breve,Motivo del cambio`
   
3. Al finalizar tu respuesta al usuario, debes mencionar que el cambio fue registrado exitosamente.
