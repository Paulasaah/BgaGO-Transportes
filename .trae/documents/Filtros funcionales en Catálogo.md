## Objetivo
- Hacer que los chips del catálogo filtren por tipo (Todos, Bicicletas, Motos, Patinetas).
- Resaltar en azul el chip seleccionado y que los demás vuelvan al estilo normal al cambiar.

## Enfoque
- Filtrado en servidor usando query param `tipo` para coherencia con BD y scopes existentes.
- Los chips serán enlaces (`<a>`) que apuntan a `route('catalogo', ['tipo' => ...])`.
- El estado activo se determina comparando `request('tipo')` con el valor del chip.

## Cambios en Vistas
- `resources/views/components/catalog/filters.blade.php` (líneas 16–26):
  - Sustituir el `x-data` y los `<button>` por `<a href="...">`.
  - Generar lista de filtros con pares `label`/`value`:
    - Todos → `null` (sin parámetro)
    - Bicicletas → `bicicleta`
    - Motos → `moto`
    - Patinetas → `patineta`
  - Aplicar clases según activo: activo `bg-blue-600 text-white`; inactivo `bg-zinc-800 text-zinc-300 hover:bg-zinc-700`.
- `resources/views/landing/catalogo_secundario.blade.php`:
  - Calcular `$tipo = request('tipo')` y pasar `activeFilter` al componente de filtros (líneas 16–20).
  - Filtrar la consulta de vehículos (líneas 26–29):
    - `\App\Models\Vehicle::visiblesEnCatalogo()->when($tipo, fn($q)=>$q->porTipo($tipo))->with('branch')->orderBy('marca')->get();`

## Lógica de Servidor
- Usar scopes existentes en `app/Models/Vehicle.php`:
  - `scopePorTipo` en `app/Models/Vehicle.php:111`.
  - `scopeVisiblesEnCatalogo` en `app/Models/Vehicle.php:121`.
- Validar el parámetro `tipo` contra el enum si se desea robustez (`\App\Enums\VehicleType::cases()`).

## UX y Accesibilidad
- Enlaces facilitan navegación/SEO y permiten compartir URL con filtro aplicado.
- Mantener la misma apariencia de chips; solo cambia la lógica a enlaces.
- Si un filtro no tiene resultados, mantener la tarjeta de "Próximamente" que ya existe.

## Pruebas y Validación
- Navegar a `catalogo` y hacer clic en cada chip; verificar:
  - URL cambia (`?tipo=...`).
  - Chip activo se ve azul; los demás vuelven a estilo normal.
  - Grid muestra solo vehículos del tipo seleccionado.
- Probar `?tipo` inválido: debe comportarse como "Todos".

¿Confirmas que avance con estos cambios?