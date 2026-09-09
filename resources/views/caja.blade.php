@extends('layouts.app')

@section('title', 'Caja')

@section('content')

    <style>
        .caja-pagina { max-width: 1280px; }
        .caja-encabezado { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
        .caja-encabezado h1 { margin: 0 0 8px; }
        .caja-meta { display: flex; flex-wrap: wrap; gap: 10px 18px; color: #555; font-size: 14px; }
        .caja-estado { display: inline-flex; align-items: center; gap: 7px; font-weight: bold; color: #176b3a; }
        .caja-estado::before { width: 9px; height: 9px; border-radius: 50%; background: #28a745; content: ''; }
        .caja-acciones { display: flex; flex-wrap: wrap; gap: 8px; }
        .boton-caja { display: inline-block; padding: 9px 14px; background: #eee; border: 1px solid #ccc; border-radius: 4px; color: #000; cursor: pointer; font-size: 14px; text-decoration: none; transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease; }
        .boton-caja:hover { background: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, .2); transform: translateY(-2px); }
        .boton-caja-principal { background: #d7ebff; border-color: #9ec8eb; }
        .caja-resumen { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 32px; }
        .caja-tarjeta { min-height: 124px; padding: 20px; border: 1px solid #ddd; border-radius: 6px; background: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, .05); }
        .caja-tarjeta-etiqueta { margin-bottom: 14px; color: #666; font-size: 14px; }
        .caja-tarjeta-valor { font-size: 25px; font-weight: bold; color: #222; }
        .caja-tarjeta.ingresos .caja-tarjeta-valor { color: #176b3a; }
        .caja-tarjeta.egresos .caja-tarjeta-valor { color: #a83a3a; }
        .caja-seccion { margin-top: 28px; padding: 24px; border: 1px solid #ddd; background: #fff; }
        .caja-seccion h2 { margin: 0 0 18px; font-size: 21px; }
        .caja-herramientas { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 18px; }
        .caja-busqueda { width: min(300px, 100%); padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
        .caja-filtro { padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; background: #fff; font: inherit; }
        .caja-herramientas .boton-caja { margin-left: auto; }
        .caja-tabla { width: 100%; border-collapse: collapse; }
        .caja-tabla th, .caja-tabla td { padding: 11px 10px; border-bottom: 1px solid #eee; text-align: left; }
        .caja-tabla th { color: #555; font-size: 13px; font-weight: bold; }
        .caja-tabla tbody tr:hover { background: #f8fbfe; }
        .caja-tipo { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .caja-tipo.ingreso { background: #d1e7dd; color: #0f5132; }
        .caja-tipo.egreso { background: #f8d7da; color: #842029; }
        .caja-monto.ingreso { color: #176b3a; font-weight: bold; }
        .caja-monto.egreso { color: #a83a3a; font-weight: bold; }
        .caja-accion-ojo { padding: 5px 9px; background: #eee; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 15px; transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease; }
        .caja-accion-ojo:hover { background: #d7ebff; box-shadow: 0 4px 10px rgba(0, 91, 170, .2); transform: translateY(-2px); }
        .caja-vacia { padding: 24px 10px; color: #666; text-align: center; }
        .modal-caja { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0, 0, 0, .35); z-index: 900; }
        .modal-caja.oculto { display: none; }
        .modal-caja-contenido { width: min(620px, 100%); max-height: 90vh; overflow-y: auto; padding: 28px; background: #fff; box-shadow: 0 8px 24px rgba(0, 0, 0, .25); }
        .modal-caja-encabezado { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .modal-caja-encabezado h2 { margin: 0; }
        .cerrar-modal-caja { padding: 4px 10px; font-size: 22px; }
        .campo-caja { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .campo-caja input, .campo-caja select, .campo-caja textarea { width: 100%; padding: 9px 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
        .campo-caja textarea { min-height: 90px; resize: vertical; }
        .caja-resumen-cierre { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; margin-bottom: 22px; }
        .caja-resumen-cierre div { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #eee; }
        .caja-resumen-cierre strong { color: #222; }
        @media (max-width: 850px) {
            .caja-encabezado { align-items: flex-start; flex-direction: column; }
            .caja-resumen { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .caja-herramientas .boton-caja { margin-left: 0; }
        }
        @media (max-width: 600px) {
            .caja-resumen { grid-template-columns: 1fr; }
            .caja-seccion { padding: 16px; overflow-x: auto; }
            .caja-tabla { min-width: 680px; }
            .caja-resumen-cierre { grid-template-columns: 1fr; }
        }
    </style>

    @php
        $movimientos = [
            ['id' => 1, 'fecha' => '09/09/2026', 'concepto' => 'Venta #25', 'tipo' => 'Ingreso', 'medio' => 'Efectivo', 'monto' => 20000, 'observacion' => 'Venta registrada'],
            ['id' => 2, 'fecha' => '09/09/2026', 'concepto' => 'Venta #26', 'tipo' => 'Ingreso', 'medio' => 'Transferencia', 'monto' => 15000, 'observacion' => 'Venta registrada'],
            ['id' => 3, 'fecha' => '09/09/2026', 'concepto' => 'Compra #12', 'tipo' => 'Egreso', 'medio' => 'Efectivo', 'monto' => 8000, 'observacion' => 'Compra registrada'],
            ['id' => 4, 'fecha' => '09/09/2026', 'concepto' => 'Retiro de dinero', 'tipo' => 'Egreso', 'medio' => 'Efectivo', 'monto' => 5000, 'observacion' => 'Retiro manual'],
        ];
        $cierres = [
            ['id' => 1, 'fecha' => '08/09/2026', 'inicial' => 50000, 'ingresos' => 125500, 'egresos' => 42300, 'final' => 133200, 'diferencia' => 0, 'estado' => 'Cerrada'],
        ];
    @endphp

    <div class="caja-pagina">
        <div class="caja-encabezado">
            <div>
                <h1>Caja</h1>
                <div class="caja-meta">
                    <span>Fecha: {{ now()->format('d/m/Y') }}</span>
                    <span class="caja-estado">Caja abierta</span>
                </div>
            </div>
            <div class="caja-acciones">
                <button type="button" class="boton-caja boton-caja-principal" data-abrir-modal="modal-apertura-caja">Abrir caja</button>
                <button type="button" class="boton-caja" data-abrir-modal="modal-cierre-caja">Cerrar caja</button>
            </div>
        </div>

        <section class="caja-resumen" aria-label="Resumen de caja">
            <article class="caja-tarjeta"><div class="caja-tarjeta-etiqueta">Saldo inicial</div><div class="caja-tarjeta-valor">$50.000</div></article>
            <article class="caja-tarjeta ingresos"><div class="caja-tarjeta-etiqueta">Ingresos</div><div class="caja-tarjeta-valor">$125.500</div></article>
            <article class="caja-tarjeta egresos"><div class="caja-tarjeta-etiqueta">Egresos</div><div class="caja-tarjeta-valor">$42.300</div></article>
            <article class="caja-tarjeta"><div class="caja-tarjeta-etiqueta">Saldo actual</div><div class="caja-tarjeta-valor">$133.200</div></article>
        </section>

        <section class="caja-seccion">
            <h2>Movimientos de caja</h2>
            <div class="caja-herramientas">
                <input type="search" id="buscar-movimiento-caja" class="caja-busqueda" placeholder="Buscar por concepto o medio">
                <select id="filtro-tipo-caja" class="caja-filtro" aria-label="Filtrar por tipo"><option value="Todos">Todos</option><option value="Ingreso">Ingresos</option><option value="Egreso">Egresos</option></select>
                <select id="filtro-medio-caja" class="caja-filtro" aria-label="Filtrar por medio de pago"><option value="Todos">Todos</option><option value="Efectivo">Efectivo</option><option value="Tarjeta">Tarjeta</option><option value="Transferencia">Transferencia</option></select>
                <button type="button" class="boton-caja" data-abrir-modal="modal-movimiento-caja">+ Movimiento</button>
            </div>
            <table class="caja-tabla"><thead><tr><th>Fecha</th><th>Concepto</th><th>Tipo</th><th>Medio</th><th>Monto</th><th>Acciones</th></tr></thead>
                <tbody id="filas-movimientos-caja"></tbody>
            </table>
        </section>

        <section class="caja-seccion">
            <h2>Historial de cierres</h2>
            <table class="caja-tabla"><thead><tr><th></th><th>Fecha</th><th>Saldo inicial</th><th>Ingresos</th><th>Egresos</th><th>Saldo final</th><th>Diferencia</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach ($cierres as $cierre)
                        <tr><td><button type="button" class="caja-accion-ojo" data-cierre="{{ $cierre['id'] }}" aria-label="Ver cierre">&#128065;</button></td><td>{{ $cierre['fecha'] }}</td><td>${{ number_format($cierre['inicial'], 0, ',', '.') }}</td><td class="caja-monto ingreso">+${{ number_format($cierre['ingresos'], 0, ',', '.') }}</td><td class="caja-monto egreso">-${{ number_format($cierre['egresos'], 0, ',', '.') }}</td><td>${{ number_format($cierre['final'], 0, ',', '.') }}</td><td>${{ number_format($cierre['diferencia'], 0, ',', '.') }}</td><td>{{ $cierre['estado'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>

    <div id="modal-movimiento-caja" class="modal-caja oculto" aria-hidden="true">
        <section class="modal-caja-contenido" role="dialog" aria-modal="true" aria-labelledby="titulo-movimiento-caja">
            <div class="modal-caja-encabezado"><h2 id="titulo-movimiento-caja">Nuevo movimiento</h2><button type="button" class="cerrar-modal-caja" data-cerrar-modal="modal-movimiento-caja" aria-label="Cerrar">&times;</button></div>
            <form id="form-movimiento-caja">
                <div class="campo-caja"><label for="tipo-movimiento-caja">Tipo</label><select id="tipo-movimiento-caja" name="tipo"><option>Ingreso</option><option>Egreso</option></select></div>
                <div class="campo-caja"><label for="concepto-movimiento-caja">Concepto</label><input id="concepto-movimiento-caja" name="concepto" required></div>
                <div class="campo-caja"><label for="medio-movimiento-caja">Medio de pago</label><select id="medio-movimiento-caja" name="medio"><option>Efectivo</option><option>Tarjeta</option><option>Transferencia</option></select></div>
                <div class="campo-caja"><label for="monto-movimiento-caja">Monto</label><input id="monto-movimiento-caja" name="monto" type="number" min="0" step="0.01" required></div>
                <div class="campo-caja"><label for="observacion-movimiento-caja">Observación (opcional)</label><textarea id="observacion-movimiento-caja" name="observacion"></textarea></div>
                <button type="button" class="boton-caja" data-cerrar-modal="modal-movimiento-caja">Cancelar</button>
                <button type="submit" class="boton-caja boton-caja-principal">Guardar</button>
            </form>
        </section>
    </div>

    <div id="modal-apertura-caja" class="modal-caja oculto" aria-hidden="true">
        <section class="modal-caja-contenido" role="dialog" aria-modal="true" aria-labelledby="titulo-apertura-caja">
            <div class="modal-caja-encabezado"><h2 id="titulo-apertura-caja">Abrir caja</h2><button type="button" class="cerrar-modal-caja" data-cerrar-modal="modal-apertura-caja" aria-label="Cerrar">&times;</button></div>
            <p>Esta pantalla está preparada para registrar el saldo inicial de una jornada.</p>
            <div class="campo-caja"><label for="saldo-inicial-caja">Saldo inicial</label><input id="saldo-inicial-caja" type="number" min="0" step="0.01" value="50000"></div>
            <button type="button" class="boton-caja" data-cerrar-modal="modal-apertura-caja">Cancelar</button><button type="button" class="boton-caja boton-caja-principal" data-cerrar-modal="modal-apertura-caja">Confirmar apertura</button>
        </section>
    </div>

    <div id="modal-cierre-caja" class="modal-caja oculto" aria-hidden="true">
        <section class="modal-caja-contenido" role="dialog" aria-modal="true" aria-labelledby="titulo-cierre-caja">
            <div class="modal-caja-encabezado"><h2 id="titulo-cierre-caja">Cerrar caja</h2><button type="button" class="cerrar-modal-caja" data-cerrar-modal="modal-cierre-caja" aria-label="Cerrar">&times;</button></div>
            <div class="caja-resumen-cierre"><div><span>Saldo inicial</span><strong>$50.000</strong></div><div><span>Ingresos</span><strong class="caja-monto ingreso">+$125.500</strong></div><div><span>Egresos</span><strong class="caja-monto egreso">-$42.300</strong></div><div><span>Saldo esperado</span><strong>$133.200</strong></div></div>
            <div class="campo-caja"><label for="dinero-contado-caja">Dinero contado</label><input id="dinero-contado-caja" type="number" min="0" step="0.01" value="133200"></div>
            <button type="button" class="boton-caja" data-cerrar-modal="modal-cierre-caja">Cancelar</button><button type="button" class="boton-caja boton-caja-principal" data-cerrar-modal="modal-cierre-caja">Confirmar cierre</button>
        </section>
    </div>

    <div id="modal-detalle-movimiento-caja" class="modal-caja oculto" aria-hidden="true"><section class="modal-caja-contenido"><div class="modal-caja-encabezado"><h2 id="titulo-detalle-movimiento-caja">Detalle del movimiento</h2><button type="button" class="cerrar-modal-caja" data-cerrar-modal="modal-detalle-movimiento-caja" aria-label="Cerrar">&times;</button></div><div id="contenido-detalle-movimiento-caja"></div></section></div>

    <script type="application/json" id="datos-movimientos-caja">@json($movimientos)</script>
    <script>
        (function () {
            const movimientos = JSON.parse(document.getElementById('datos-movimientos-caja').textContent);
            const filas = document.getElementById('filas-movimientos-caja');
            const busqueda = document.getElementById('buscar-movimiento-caja');
            const filtroTipo = document.getElementById('filtro-tipo-caja');
            const filtroMedio = document.getElementById('filtro-medio-caja');
            const modales = document.querySelectorAll('.modal-caja');

            function dinero(valor) { return '$' + Number(valor).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }); }
            function renderizarMovimientos() {
                const texto = busqueda.value.trim().toLowerCase();
                const resultados = movimientos.filter(function (movimiento) {
                    return (!texto || (movimiento.concepto + ' ' + movimiento.medio).toLowerCase().includes(texto)) && (filtroTipo.value === 'Todos' || movimiento.tipo === filtroTipo.value) && (filtroMedio.value === 'Todos' || movimiento.medio === filtroMedio.value);
                });
                filas.innerHTML = resultados.length ? resultados.map(function (movimiento) { const ingreso = movimiento.tipo === 'Ingreso'; return '<tr><td>' + movimiento.fecha + '</td><td>' + movimiento.concepto + '</td><td><span class="caja-tipo ' + (ingreso ? 'ingreso' : 'egreso') + '">' + movimiento.tipo + '</span></td><td>' + movimiento.medio + '</td><td class="caja-monto ' + (ingreso ? 'ingreso' : 'egreso') + '">' + (ingreso ? '+' : '-') + dinero(movimiento.monto) + '</td><td><button type="button" class="caja-accion-ojo" data-movimiento="' + movimiento.id + '" aria-label="Ver movimiento">&#128065;</button></td></tr>'; }).join('') : '<tr><td colspan="6" class="caja-vacia">No hay movimientos que coincidan con los filtros.</td></tr>';
            }
            function abrir(id) { const modal = document.getElementById(id); if (modal) { modal.classList.remove('oculto'); modal.setAttribute('aria-hidden', 'false'); } }
            function cerrar(id) { const modal = document.getElementById(id); if (modal) { modal.classList.add('oculto'); modal.setAttribute('aria-hidden', 'true'); } }
            document.querySelectorAll('[data-abrir-modal]').forEach(function (boton) { boton.addEventListener('click', function () { abrir(boton.dataset.abrirModal); }); });
            document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) { boton.addEventListener('click', function () { cerrar(boton.dataset.cerrarModal); }); });
            modales.forEach(function (modal) { modal.addEventListener('click', function (event) { if (event.target === modal) cerrar(modal.id); }); });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape') modales.forEach(function (modal) { if (!modal.classList.contains('oculto')) cerrar(modal.id); }); });
            [busqueda, filtroTipo, filtroMedio].forEach(function (control) { control.addEventListener('input', renderizarMovimientos); control.addEventListener('change', renderizarMovimientos); });
            filas.addEventListener('click', function (event) { const boton = event.target.closest('[data-movimiento]'); if (!boton) return; const movimiento = movimientos.find(function (item) { return String(item.id) === boton.dataset.movimiento; }); if (movimiento) { document.getElementById('contenido-detalle-movimiento-caja').innerHTML = '<p><strong>Concepto:</strong> ' + movimiento.concepto + '</p><p><strong>Fecha:</strong> ' + movimiento.fecha + '</p><p><strong>Tipo:</strong> ' + movimiento.tipo + '</p><p><strong>Medio:</strong> ' + movimiento.medio + '</p><p><strong>Monto:</strong> ' + dinero(movimiento.monto) + '</p><p><strong>Observación:</strong> ' + movimiento.observacion + '</p>'; abrir('modal-detalle-movimiento-caja'); } });
            document.getElementById('form-movimiento-caja').addEventListener('submit', function (event) { event.preventDefault(); cerrar('modal-movimiento-caja'); });
            renderizarMovimientos();
        })();
    </script>

@endsection
