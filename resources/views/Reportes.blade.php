@extends('layouts.app')

@section('title', 'Reportes')

@section('content')

<style>
    .reportes-pagina {
        max-width: 1280px;
        margin: 0 auto;
    }

    .reportes-encabezado {
        margin-bottom: 28px;
    }

    .reportes-encabezado h1 {
        margin: 0 0 7px;
        color: #222;
        font-size: 30px;
    }

    .reportes-encabezado p {
        margin: 0;
        color: #666;
    }

    .reportes-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .reporte-card {
        min-width: 0;
        padding: 24px;
        border: 1px solid #ddd;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .reporte-card-grande {
        grid-column: 1 / -1;
    }

    .reporte-encabezado {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .reporte-titulo {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .reporte-titulo h2 {
        margin: 0;
        color: #222;
        font-size: 21px;
    }

    .reporte-titulo-icono {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 29px;
        height: 29px;
        border-radius: 6px;
        background: #eaf3fb;
        color: #1769aa;
        font-size: 16px;
    }

    .periodo-reporte {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 7px;
    }

    .periodo-reporte select,
    .periodo-reporte input {
        min-width: 112px;
        padding: 8px 9px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
        color: #333;
        font: inherit;
        font-size: 13px;
    }

    .periodo-reporte .periodo-tipo {
        min-width: 86px;
    }

    .reporte-resumen {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .reporte-metrica {
        padding: 13px 15px;
        border: 1px solid #e5e5e5;
        background: #fafafa;
    }

    .reporte-metrica span {
        display: block;
        margin-bottom: 7px;
        color: #666;
        font-size: 13px;
    }

    .reporte-metrica strong {
        display: block;
        color: #222;
        font-size: 23px;
    }

    .reporte-metrica small {
        display: block;
        margin-top: 5px;
        color: #176b3a;
        font-size: 12px;
    }

    .reporte-metrica small.negativo {
        color: #a83a3a;
    }

    .grafico-reporte {
        min-height: 218px;
        padding: 15px 12px 7px;
        border: 1px solid #e8e8e8;
        background: #fff;
    }

    .grafico-linea {
        width: 100%;
        height: 174px;
    }

    .grafico-linea .linea-principal {
        fill: none;
        stroke: #2676b9;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 3;
    }

    .grafico-linea .area-principal {
        fill: rgba(38, 118, 185, .11);
    }

    .grafico-linea .punto {
        fill: #fff;
        stroke: #2676b9;
        stroke-width: 2;
    }

    .grafico-linea .linea-secundaria {
        fill: none;
        stroke: #58a27a;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 3;
    }

    .grafico-linea .punto-secundario {
        fill: #fff;
        stroke: #58a27a;
        stroke-width: 2;
    }

    .grafico-linea .guia {
        stroke: #edf0f2;
        stroke-width: 1;
    }

    .grafico-etiquetas {
        display: flex;
        justify-content: space-between;
        margin-top: 1px;
        color: #888;
        font-size: 11px;
    }

    .grafico-barras {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 190px;
        padding: 14px 8px 0;
        border-bottom: 1px solid #dfe3e6;
        background: repeating-linear-gradient(to bottom, transparent 0, transparent 45px, #edf0f2 46px, transparent 47px);
    }

    .barra-grupo {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        width: 12%;
        height: 100%;
        gap: 4px;
    }

    .barra {
        width: min(22px, 42%);
        min-height: 5px;
        border-radius: 3px 3px 0 0;
        background: #2676b9;
    }

    .barra.secundaria {
        background: #9abfd9;
    }

    .barra.verde {
        background: #58a27a;
    }

    .grafico-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 12px;
        color: #666;
        font-size: 12px;
    }

    .grafico-leyenda span::before {
        display: inline-block;
        width: 9px;
        height: 9px;
        margin-right: 5px;
        border-radius: 2px;
        background: #2676b9;
        content: '';
    }

    .grafico-leyenda .leyenda-secundaria::before {
        background: #9abfd9;
    }

    .grafico-leyenda .leyenda-verde::before {
        background: #58a27a;
    }

    .cuentas-resumen {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 18px;
    }

    .cuenta-metrica {
        padding: 12px;
        border-left: 3px solid #2676b9;
        background: #f8fafb;
    }

    .cuenta-metrica.deuda-proveedor {
        border-left-color: #d18a3a;
    }

    .cuenta-metrica.cobro {
        border-left-color: #58a27a;
    }

    .cuenta-metrica.pago {
        border-left-color: #8b78a8;
    }

    .cuenta-metrica span {
        display: block;
        margin-bottom: 6px;
        color: #666;
        font-size: 12px;
    }

    .cuenta-metrica strong {
        color: #222;
        font-size: 17px;
    }

    @media (max-width: 900px) {
        .reportes-grid {
            grid-template-columns: 1fr;
        }

        .reporte-card-grande {
            grid-column: auto;
        }
    }

    @media (max-width: 650px) {
        main {
            padding: 20px 14px;
        }

        .reporte-card {
            padding: 17px;
        }

        .reporte-encabezado {
            flex-direction: column;
        }

        .periodo-reporte {
            justify-content: flex-start;
            width: 100%;
        }

        .periodo-reporte select,
        .periodo-reporte input {
            flex: 1;
            min-width: 0;
        }

        .cuentas-resumen {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grafico-reporte {
            overflow: hidden;
        }
    }
</style>

<div class="reportes-pagina">
    <div class="reportes-encabezado">
        <h1>Reportes</h1>
        <p>Analizá el rendimiento y la situación del comercio por diferentes períodos.</p>
    </div>

    <div class="reportes-grid">
        <section class="reporte-card reporte-card-grande" aria-labelledby="titulo-reporte-ventas">
            <div class="reporte-encabezado">
                <div class="reporte-titulo">
                    <span class="reporte-titulo-icono" aria-hidden="true">&#8599;</span>
                    <h2 id="titulo-reporte-ventas">Ventas</h2>
                </div>
                <div class="periodo-reporte" aria-label="Período de ventas">
                    <select class="periodo-tipo" aria-label="Tipo de período de ventas"><option>Día</option><option>Semana</option><option selected>Mes</option><option>Año</option></select>
                    <select aria-label="Fecha del período de ventas"><option>Septiembre 2026</option><option>Agosto 2026</option><option>Julio 2026</option></select>
                </div>
            </div>
            <div class="reporte-resumen">
                <div class="reporte-metrica"><span>Total vendido</span><strong>$1.245.800</strong><small>+12,5% vs. período anterior</small></div>
                <div class="reporte-metrica"><span>Cantidad de ventas</span><strong>184</strong><small>6,1 ventas por día</small></div>
            </div>
            <div class="grafico-reporte">
                <svg class="grafico-linea" viewBox="0 0 720 174" role="img" aria-label="Evolución ficticia de ventas durante septiembre">
                    <line class="guia" x1="0" y1="28" x2="720" y2="28"/><line class="guia" x1="0" y1="72" x2="720" y2="72"/><line class="guia" x1="0" y1="116" x2="720" y2="116"/><line class="guia" x1="0" y1="160" x2="720" y2="160"/>
                    <path class="area-principal" d="M0 136 L80 119 L160 126 L240 85 L320 105 L400 68 L480 80 L560 48 L640 66 L720 25 L720 174 L0 174 Z"/>
                    <path class="linea-principal" d="M0 136 L80 119 L160 126 L240 85 L320 105 L400 68 L480 80 L560 48 L640 66 L720 25"/>
                    <circle class="punto" cx="0" cy="136" r="4"/><circle class="punto" cx="240" cy="85" r="4"/><circle class="punto" cx="480" cy="80" r="4"/><circle class="punto" cx="720" cy="25" r="4"/>
                </svg>
                <div class="grafico-etiquetas"><span>01 Sep</span><span>08 Sep</span><span>15 Sep</span><span>22 Sep</span><span>30 Sep</span></div>
            </div>
        </section>

        <section class="reporte-card" aria-labelledby="titulo-reporte-compras">
            <div class="reporte-encabezado">
                <div class="reporte-titulo"><span class="reporte-titulo-icono" aria-hidden="true">&#128230;</span><h2 id="titulo-reporte-compras">Compras</h2></div>
                <div class="periodo-reporte" aria-label="Período de compras"><select class="periodo-tipo" aria-label="Tipo de período de compras"><option>Día</option><option>Semana</option><option selected>Mes</option><option>Año</option></select><select aria-label="Fecha del período de compras"><option>Septiembre 2026</option><option>Agosto 2026</option></select></div>
            </div>
            <div class="reporte-resumen"><div class="reporte-metrica"><span>Total comprado</span><strong>$624.300</strong><small>+4,8% vs. período anterior</small></div><div class="reporte-metrica"><span>Cantidad de compras</span><strong>42</strong><small>Compras registradas</small></div></div>
            <div class="grafico-reporte"><div class="grafico-barras"><div class="barra-grupo"><i class="barra" style="height: 45%"></i></div><div class="barra-grupo"><i class="barra" style="height: 62%"></i></div><div class="barra-grupo"><i class="barra" style="height: 38%"></i></div><div class="barra-grupo"><i class="barra" style="height: 78%"></i></div><div class="barra-grupo"><i class="barra" style="height: 55%"></i></div><div class="barra-grupo"><i class="barra" style="height: 88%"></i></div><div class="barra-grupo"><i class="barra" style="height: 66%"></i></div></div><div class="grafico-etiquetas"><span>01 Sep</span><span>07 Sep</span><span>14 Sep</span><span>21 Sep</span><span>30 Sep</span></div></div>
        </section>

        <section class="reporte-card" aria-labelledby="titulo-reporte-ganancias">
            <div class="reporte-encabezado">
                <div class="reporte-titulo"><span class="reporte-titulo-icono" aria-hidden="true">&#36;</span><h2 id="titulo-reporte-ganancias">Ganancias</h2></div>
                <div class="periodo-reporte" aria-label="Período de ganancias"><select class="periodo-tipo" aria-label="Tipo de período de ganancias"><option>Día</option><option>Semana</option><option selected>Mes</option><option>Año</option></select><select aria-label="Fecha del período de ganancias"><option>Septiembre 2026</option><option>Agosto 2026</option></select></div>
            </div>
            <div class="reporte-resumen"><div class="reporte-metrica"><span>Ganancia total</span><strong>$621.500</strong><small>Dato ficticio de referencia</small></div><div class="reporte-metrica"><span>Evolución</span><strong>49,9%</strong><small>Sobre ventas del período</small></div></div>
            <div class="grafico-reporte"><svg class="grafico-linea" viewBox="0 0 520 174" role="img" aria-label="Evolución ficticia de ganancias"><line class="guia" x1="0" y1="28" x2="520" y2="28"/><line class="guia" x1="0" y1="72" x2="520" y2="72"/><line class="guia" x1="0" y1="116" x2="520" y2="116"/><line class="guia" x1="0" y1="160" x2="520" y2="160"/><path class="linea-secundaria" d="M0 130 L75 114 L150 123 L225 76 L300 93 L375 64 L450 75 L520 34"/><circle class="punto-secundario" cx="0" cy="130" r="4"/><circle class="punto-secundario" cx="225" cy="76" r="4"/><circle class="punto-secundario" cx="520" cy="34" r="4"/></svg><div class="grafico-etiquetas"><span>01 Sep</span><span>08 Sep</span><span>15 Sep</span><span>22 Sep</span><span>30 Sep</span></div></div>
        </section>

        <section class="reporte-card reporte-card-grande" aria-labelledby="titulo-reporte-cuentas">
            <div class="reporte-encabezado"><div class="reporte-titulo"><span class="reporte-titulo-icono" aria-hidden="true">&#128210;</span><h2 id="titulo-reporte-cuentas">Cuentas corrientes</h2></div><div class="periodo-reporte" aria-label="Período de cuentas corrientes"><select class="periodo-tipo" aria-label="Tipo de período de cuentas corrientes"><option>Día</option><option>Semana</option><option selected>Mes</option><option>Año</option></select><select aria-label="Fecha del período de cuentas corrientes"><option>Septiembre 2026</option><option>Agosto 2026</option></select></div></div>
            <div class="cuentas-resumen"><div class="cuenta-metrica"><span>Adeudado por clientes</span><strong>$80.000</strong></div><div class="cuenta-metrica deuda-proveedor"><span>Adeudado a proveedores</span><strong>$120.000</strong></div><div class="cuenta-metrica cobro"><span>Cobros realizados</span><strong>$46.500</strong></div><div class="cuenta-metrica pago"><span>Pagos realizados</span><strong>$72.300</strong></div></div>
            <div class="grafico-reporte"><svg class="grafico-linea" viewBox="0 0 720 174" role="img" aria-label="Resumen ficticio de cuentas corrientes"><line class="guia" x1="0" y1="28" x2="720" y2="28"/><line class="guia" x1="0" y1="72" x2="720" y2="72"/><line class="guia" x1="0" y1="116" x2="720" y2="116"/><line class="guia" x1="0" y1="160" x2="720" y2="160"/><path class="linea-principal" d="M0 92 L100 76 L200 83 L300 62 L400 70 L500 43 L600 52 L720 31"/><path class="linea-secundaria" d="M0 138 L100 121 L200 129 L300 110 L400 118 L500 98 L600 105 L720 82"/></svg><div class="grafico-etiquetas"><span>01 Sep</span><span>07 Sep</span><span>14 Sep</span><span>21 Sep</span><span>30 Sep</span></div><div class="grafico-leyenda"><span>Deuda de clientes</span><span class="leyenda-verde">Cobros realizados</span></div></div>
        </section>

        <section class="reporte-card reporte-card-grande" aria-labelledby="titulo-reporte-stock">
            <div class="reporte-encabezado"><div class="reporte-titulo"><span class="reporte-titulo-icono" aria-hidden="true">&#128230;</span><h2 id="titulo-reporte-stock">Stock</h2></div><div class="periodo-reporte" aria-label="Período de stock"><select class="periodo-tipo" aria-label="Tipo de período de stock"><option>Día</option><option>Semana</option><option selected>Mes</option><option>Año</option></select><select aria-label="Fecha del período de stock"><option>Septiembre 2026</option><option>Agosto 2026</option></select></div></div>
            <div class="reporte-resumen"><div class="reporte-metrica"><span>Entradas de stock</span><strong>248 unidades</strong><small>36 movimientos</small></div><div class="reporte-metrica"><span>Salidas de stock</span><strong>192 unidades</strong><small class="negativo">29 movimientos</small></div><div class="reporte-metrica"><span>Ajustes</span><strong>12 unidades</strong><small>8 movimientos</small></div><div class="reporte-metrica"><span>Productos con stock bajo</span><strong>7</strong><small class="negativo">Requieren atención</small></div></div>
            <div class="grafico-reporte"><div class="grafico-barras"><div class="barra-grupo"><i class="barra" style="height: 74%"></i><i class="barra secundaria" style="height: 42%"></i></div><div class="barra-grupo"><i class="barra" style="height: 55%"></i><i class="barra secundaria" style="height: 67%"></i></div><div class="barra-grupo"><i class="barra" style="height: 82%"></i><i class="barra secundaria" style="height: 51%"></i></div><div class="barra-grupo"><i class="barra" style="height: 45%"></i><i class="barra secundaria" style="height: 78%"></i></div><div class="barra-grupo"><i class="barra" style="height: 91%"></i><i class="barra secundaria" style="height: 61%"></i></div><div class="barra-grupo"><i class="barra" style="height: 65%"></i><i class="barra secundaria" style="height: 48%"></i></div><div class="barra-grupo"><i class="barra" style="height: 78%"></i><i class="barra secundaria" style="height: 72%"></i></div></div><div class="grafico-etiquetas"><span>01 Sep</span><span>07 Sep</span><span>14 Sep</span><span>21 Sep</span><span>30 Sep</span></div><div class="grafico-leyenda"><span>Entradas</span><span class="leyenda-secundaria">Salidas</span></div></div>
        </section>
    </div>
</div>

@endsection