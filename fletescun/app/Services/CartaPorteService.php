<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\IOFactory;

/**
 * CartaPorteService - Generador de Cartas Porte en Word
 * 
 * Crea documentos Word con datos logísticos y contrato de servicios
 */
class CartaPorteService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Genera el documento Word para una cotización
     */
    public function generar(string $cotizacionId): array
    {
        try {
            // Datos básicos
            $cotizacion = DB::table('cotizaciones')->where('id', $cotizacionId)->first();
            
            if (!$cotizacion) {
                throw new \Exception("Cotización no encontrada: {$cotizacionId}");
            }

            $cliente = DB::table('clientes')->where('id', $cotizacion->cliente_id)->first();
            
            if (!$cliente) {
                throw new \Exception("Cliente no encontrado para cotización: {$cotizacionId}");
            }

            $inventario = DB::table('inventario_articulos')
                ->where('cotizacion_id', $cotizacionId)
                ->orderBy('orden')
                ->get();
            
            $servicios = DB::table('servicios_adicionales')
                ->where('cotizacion_id', $cotizacionId)
                ->pluck('servicio')
                ->toArray();
            
            $precios = $this->pricingService->calcularPrecioFinal($cotizacionId);

            // Validar que exista información
            if (!$precios) {
                throw new \Exception("No se pudieron calcular los precios para: {$cotizacionId}");
            }

            // Crear documento
            $phpWord = new PhpWord();
            $this->agregarHoja1($phpWord, $cotizacion, $cliente, $inventario, $servicios, $precios);
            $this->agregarHoja2($phpWord, $cliente, $cotizacion->folio);

            $resultado = $this->guardarArchivo($phpWord, $cotizacion->folio);
            
            \Log::info("CartaPorte generada exitosamente: {$cotizacion->folio}");
            
            return $resultado;
            
        } catch (\Exception $e) {
            \Log::error("Error en CartaPorteService::generar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Agrega Página 1: Carátula con logística
     */
    private function agregarHoja1($phpWord, $cotizacion, $cliente, $inventario, $servicios, $precios): void
    {
        $section = $phpWord->addSection();

        // ENCABEZADO
        $section->addParagraph('CARTA PORTE MUDANZA FORÁNEA', ['alignment' => Jc::CENTER, 'spaceAfter' => 50]);
        $section->addParagraph('FLETESCUN', ['alignment' => Jc::CENTER, 'spaceAfter' => 50]);
        $section->addParagraph("Folio: {$cotizacion->folio}", ['alignment' => Jc::CENTER, 'spaceAfter' => 50]);
        $section->addParagraph('Lugar de expedición: Ciudad de México, ' . date('d/m/Y'), ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

        // DATOS DE ORIGEN
        $section->addParagraph('DATOS DE ORIGEN', ['spaceAfter' => 100]);
        $tbl = $section->addTable();
        $this->filaPar($tbl, 'Remitente:', $cliente->nombre ?? '');
        $this->filaPar($tbl, 'Teléfono:', $cliente->telefono ?? '');
        $this->filaPar($tbl, 'Correo:', $cliente->correo ?? '');
        $this->filaPar($tbl, 'Dirección:', $cotizacion->direccion_origen ?? '');
        $this->filaPar($tbl, 'Piso:', ($cotizacion->piso_origen ?? '—') . ($cotizacion->elevador_origen ? ' (con elevador)' : ''));
        $section->addParagraph('', ['spaceAfter' => 200]);

        // DATOS DE DESTINO
        $section->addParagraph('DATOS DE DESTINO', ['spaceAfter' => 100]);
        $tbl = $section->addTable();
        $this->filaPar($tbl, 'Destinatario:', $cliente->nombre ?? '');
        $this->filaPar($tbl, 'Dirección:', $cotizacion->direccion_destino ?? '');
        $this->filaPar($tbl, 'Piso:', ($cotizacion->piso_destino ?? '—') . ($cotizacion->elevador_destino ? ' (con elevador)' : ''));
        $section->addParagraph('', ['spaceAfter' => 200]);

        // INVENTARIO
        $section->addParagraph('INVENTARIO DECLARADO', ['spaceAfter' => 100]);
        $tbl = $section->addTable();
        $row = $tbl->addRow();
        $row->addCell(800)->addParagraph('Cantidad', ['bold' => true]);
        $row->addCell(3500)->addParagraph('Artículo', ['bold' => true]);
        $row->addCell(3500)->addParagraph('Observación', ['bold' => true]);
        foreach ($inventario as $item) {
            $row = $tbl->addRow();
            $row->addCell(800)->addParagraph((string)$item->cantidad);
            $row->addCell(3500)->addParagraph($item->nombre);
            $row->addCell(3500)->addParagraph($item->observaciones ?? '—');
        }
        $section->addParagraph('', ['spaceAfter' => 200]);

        // SERVICIOS
        if (!empty($servicios)) {
            $section->addParagraph('SERVICIOS ADICIONALES', ['spaceAfter' => 100]);
            foreach ($servicios as $s) {
                $section->addParagraph('✓ ' . $s, ['spaceAfter' => 50]);
            }
            $section->addParagraph('', ['spaceAfter' => 200]);
        }

        // PRECIOS
        $section->addParagraph('DESGLOSE DE PRECIOS', ['spaceAfter' => 100]);
        $tbl = $section->addTable();
        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('Concepto', ['bold' => true]);
        $row->addCell(2000)->addParagraph('Cantidad', ['bold' => true]);
        $row->addCell(2000)->addParagraph('Monto', ['bold' => true]);

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('Volumen (m³)');
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['costo_volumen'], 2));

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('Transporte (km)');
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['costo_distancia'], 2));

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('Maniobra por piso');
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['costo_piso'], 2));

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('Carga, descarga y protección');
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['costos_fijos'], 2));

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('SUBTOTAL BASE', ['bold' => true]);
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['subtotal_base'], 2), ['bold' => true]);

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('IVA (16%)', ['bold' => true]);
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['iva_monto'], 2), ['bold' => true]);

        $row = $tbl->addRow();
        $row->addCell(4000)->addParagraph('TOTAL FINAL', ['bold' => true]);
        $row->addCell(2000)->addParagraph('');
        $row->addCell(2000)->addParagraph('$' . number_format($precios['total_final'], 2), ['bold' => true]);
    }

    /**
     * Agrega Página 2: Contrato completo con cláusulas exactas
     */
    private function agregarHoja2($phpWord, $cliente, $folio): void
    {
        $section = $phpWord->addSection();

        // ENCABEZADO DEL CONTRATO
        $section->addParagraph('', ['spaceAfter' => 50]);
        $section->addParagraph("CONTRATO No. {$folio}", ['spaceAfter' => 100]);

        // INTRODUCCIÓN
        $section->addParagraph(
            'CONTRATO DE PRESTACIÓN DE SERVICIOS DE AUTOTRANSPORTE DE CARGA QUE CELEBRAN POR UNA PARTE MUDANZA FLETESCUN TU EMPRESA COMO "EL PROVEEDOR" Y POR LA OTRA PARTE COMO "EL CONSUMIDOR", CUYO NOMBRE, ' . strtoupper($cliente->nombre ?? 'EL CLIENTE') . ', Y DATOS CONSTAN EN LA CARÁTULA DE ESTE CONTRATO COMO PARTE INTEGRAL DEL MISMO, SUJETÁNDOSE AL TENOR DE LAS SIGUIENTES:',
            ['spaceAfter' => 150, 'align' => Jc::LEFT]
        );

        // CLÁUSULAS
        $section->addParagraph('C L Á U S U L A S', ['spaceAfter' => 150, 'alignment' => Jc::CENTER, 'bold' => true]);

        $clauses = [
            'PRIMERA' => 'El objeto del presente contrato es la prestación del servicio de Transporte de Carga, el cual podrá ser local o foráneo, según se determine en la carátula.',
            
            'SEGUNDA' => 'EL PROVEEDOR se obliga a entregar a EL CONSUMIDOR una copia del presente contrato de adhesión al momento de su firma.',
            
            'TERCERA' => 'Es obligación de EL PROVEEDOR que el personal que se encargará de las maniobras de carga y descarga de los bienes se identifique plenamente ante EL CONSUMIDOR, antes de iniciar las operaciones contratadas y especifique las maniobras junto con el inventario que viene señalado en el contrato. TODO SERVICIO ADICIONAL NO CONSIDERADO EN EL INVENTARIO Y DESCRIPCIÓN DEL SERVICIO CAUSARÁ UN CARGO ADICIONAL SUJETO AL ENCARGADO DE MANIOBRAS O AL DEPARTAMENTO DE LOGÍSTICA DE LA EMPRESA.',
            
            'CUARTA' => 'Es obligación de EL CONSUMIDOR declarar verazmente al proveedor toda la información relativa a la descripción, valor, cantidad, peso y demás características de la mercancía que pretende transportar; en caso de falsedad, EL CONSUMIDOR asumirá la responsabilidad respectiva y las repercusiones en costos adicionales.',
            
            'QUINTA' => 'Es obligación de EL PROVEEDOR entregar por escrito la Carta de Garantías respectiva a la prestación del servicio. En caso de alguna eventualidad o daño en el tipo de servicio EXCLUSIVO, la empresa solo se hace responsable de cubrir dicho imprevisto con el 5% al 15% del daño del mueble, tomando en cuenta el estado en el que se encuentra dicho objeto (menaje de casa en uso); solo aplica al siguiente día después de la entrega de su mercancía y es responsabilidad del cliente la contratación de un seguro para su carga (cubre trayecto de origen y destino y solo cubre robo o siniestro).',
            
            'SEXTA' => 'En caso de algún daño en los bienes del consumidor en el tipo de servicio COMPARTIDO, la empresa solo aplica un reembolso del 1% al 10% tomando en cuenta el daño generado a los bienes y es responsabilidad del cliente la contratación de un seguro para su carga (cubre trayecto de origen y destino y solo cubre robo o siniestro).',
            
            'SÉPTIMA' => 'La empresa no se hace responsable del funcionamiento de electrónica, línea blanca y electrodomésticos. Solo se hace responsable en caso de golpes visibles causados en las maniobras. Las plantas y cristales viajan por cuenta y riesgo del cliente; ninguna ruptura, abolladura o despostillamiento de algún ónix, piedra, mármol, etc., NO se pagará, ya que estos por su naturaleza son susceptibles a romperse.',
            
            'OCTAVA' => 'Se establece como penalización por incumplimiento de contrato para cualquiera de las partes el 15% del valor total de la operación.',
            
            'NOVENA' => 'Es responsabilidad del CLIENTE revisar el tipo de maniobras (volados, acarreos, etc.) y servicios que incluyen nuestros vendedores antes de hacer el primer depósito a cuenta de sus servicios, para evitar situaciones de conflicto.',
            
            'DÉCIMA' => 'El cliente enviará por correo o mediante un mensaje de texto el comprobante del 10% Y el 60% cuando esté el producto en la unidad Y EL 30% antes de entregar en destino.',
            
            'DÉCIMA PRIMERA' => 'La Procuraduría Federal del Consumidor es competente en la vía administrativa para resolver controversia que se suscite sobre la interpretación o cumplimiento del presente contrato; sin perjuicio de lo anterior, las partes se someten a la jurisdicción de los tribunales competentes en la Ciudad de México, renunciando expresamente a cualquier otra jurisdicción que pudiera corresponderles por razón de sus domicilios presentes o futuros o por cualquier otra razón.',
        ];

        foreach ($clauses as $num => $txt) {
            $section->addParagraph("{$num}.- {$txt}", ['spaceAfter' => 100]);
        }

        // ESPACIO PARA FIRMAS
        $section->addParagraph('', ['spaceAfter' => 300]);
        $section->addParagraph('Leído el contenido del presente contrato por parte de los interesados y sabedoras de su alcance legal, lo firman por duplicado en la Ciudad ___ a los ___ del año ___. ', ['spaceAfter' => 200]);

        // TABLA DE FIRMAS
        $tbl = $section->addTable();
        $tbl->setWidth(10000);

        $row = $tbl->addRow();
        $cell1 = $row->addCell(5000);
        $cell1->addParagraph('', ['spaceAfter' => 200]);
        $cell1->addParagraph('JAVIER ASCENCIO MÁRQUEZ', ['alignment' => Jc::CENTER, 'bold' => true]);
        $cell1->addParagraph('EL PROVEEDOR', ['alignment' => Jc::CENTER]);

        $cell2 = $row->addCell(5000);
        $cell2->addParagraph('', ['spaceAfter' => 200]);
        $cell2->addParagraph('_____________________', ['alignment' => Jc::CENTER]);
        $cell2->addParagraph($cliente->nombre ?? '', ['alignment' => Jc::CENTER, 'bold' => true]);
        $cell2->addParagraph('EL CONSUMIDOR', ['alignment' => Jc::CENTER]);
    }

    /**
     * Helper: fila de tabla (etiqueta: valor)
     */
    private function filaPar($tbl, $label, $val): void
    {
        $row = $tbl->addRow();
        $row->addCell(2000)->addParagraph($label, ['bold' => true]);
        $row->addCell(3000)->addParagraph($val);
    }

    /**
     * Guarda documento
     */
    private function guardarArchivo($phpWord, $folio): array
    {
        $file = "CartaPorte_{$folio}.docx";
        $dir = storage_path('app/public/documentos');
        @mkdir($dir, 0755, true);
        $path = "{$dir}/{$file}";

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        return [
            'nombre_archivo' => $file,
            'ruta_relativa'  => "documentos/{$file}",
            'ruta_absoluta'  => $path,
            'tamanio_bytes'  => filesize($path),
        ];
    }
}
