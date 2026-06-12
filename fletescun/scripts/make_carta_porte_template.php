<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(10);

$tableStyle = ['borderSize' => 6, 'borderColor' => 'D1D5DB', 'cellMargin' => 90];
$headerCellStyle = ['bgColor' => '1B3A6B'];
$headerTextStyle = ['bold' => true, 'color' => 'FFFFFF'];

$section = $phpWord->addSection();
$section->addText('CARTA PORTE MUDANZA FORÁNEA', ['bold' => true, 'size' => 14, 'color' => '1B3A6B']);
$section->addText('FLETESCUN', ['bold' => true, 'size' => 12, 'color' => '1B3A6B']);
$section->addText('Folio: ${folio}');
$section->addText('Fecha expedición: ${fecha_expedicion}');
$section->addTextBreak(1);

$section->addText('DATOS DE ORIGEN', ['bold' => true, 'color' => '1B3A6B']);
$table = $section->addTable($tableStyle);
$table->addRow();
$table->addCell(12000, $headerCellStyle)->addText('INFORMACION DE ORIGEN', $headerTextStyle);
$table->addRow();
$table->addCell(3000)->addText('Remitente');
$table->addCell(9000)->addText('${cliente_nombre}');
$table->addRow();
$table->addCell(3000)->addText('Telefono');
$table->addCell(9000)->addText('${cliente_telefono}');
$table->addRow();
$table->addCell(3000)->addText('Correo');
$table->addCell(9000)->addText('${cliente_correo}');
$table->addRow();
$table->addCell(3000)->addText('Direccion');
$table->addCell(9000)->addText('${origen_direccion}');
$table->addRow();
$table->addCell(3000)->addText('Piso');
$table->addCell(9000)->addText('${origen_piso}');
$section->addTextBreak(1);

$section->addText('DATOS DE DESTINO', ['bold' => true, 'color' => '1B3A6B']);
$table = $section->addTable($tableStyle);
$table->addRow();
$table->addCell(12000, $headerCellStyle)->addText('INFORMACION DE DESTINO', $headerTextStyle);
$table->addRow();
$table->addCell(3000)->addText('Destinatario');
$table->addCell(9000)->addText('${cliente_nombre}');
$table->addRow();
$table->addCell(3000)->addText('Direccion');
$table->addCell(9000)->addText('${destino_direccion}');
$table->addRow();
$table->addCell(3000)->addText('Piso');
$table->addCell(9000)->addText('${destino_piso}');
$section->addTextBreak(1);

$section->addText('INVENTARIO DECLARADO', ['bold' => true, 'color' => '1B3A6B']);
$table = $section->addTable($tableStyle);
$table->addRow();
$table->addCell(1500, $headerCellStyle)->addText('Cantidad', $headerTextStyle);
$table->addCell(6000, $headerCellStyle)->addText('Artículo', $headerTextStyle);
$table->addCell(6000, $headerCellStyle)->addText('Observación', $headerTextStyle);
$table->addRow();
$table->addCell(1500)->addText('${inv_cantidad}');
$table->addCell(6000)->addText('${inv_articulo}');
$table->addCell(6000)->addText('${inv_observacion}');
$section->addTextBreak(1);

$section->addText('SERVICIOS ADICIONALES', ['bold' => true, 'color' => '1B3A6B']);
$table = $section->addTable($tableStyle);
$table->addRow();
$table->addCell(12000, $headerCellStyle)->addText('SERVICIO', $headerTextStyle);
$table->addRow();
$table->addCell(12000)->addText('${servicio}');
$section->addTextBreak(1);

$section->addText('DESGLOSE DE PRECIOS', ['bold' => true, 'color' => '1B3A6B']);
$table = $section->addTable($tableStyle);
$table->addRow();
$table->addCell(7000, $headerCellStyle)->addText('Concepto', $headerTextStyle);
$table->addCell(3000, $headerCellStyle)->addText('Monto', $headerTextStyle);
$table->addRow();
$table->addCell(7000)->addText('Volumen (${volumen_m3} m3)');
$table->addCell(3000)->addText('${precio_volumen}');
$table->addRow();
$table->addCell(7000)->addText('Distancia (${distancia_km} km)');
$table->addCell(3000)->addText('${precio_distancia}');
$table->addRow();
$table->addCell(7000)->addText('Maniobra por piso');
$table->addCell(3000)->addText('${precio_piso}');
$table->addRow();
$table->addCell(7000)->addText('Costos fijos');
$table->addCell(3000)->addText('${precio_fijos}');
$table->addRow();
$table->addCell(7000)->addText('Subtotal base');
$table->addCell(3000)->addText('${subtotal_base}');
$table->addRow();
$table->addCell(7000)->addText('IVA (${iva_pct}%)');
$table->addCell(3000)->addText('${iva_monto}');
$table->addRow();
$table->addCell(7000, $headerCellStyle)->addText('TOTAL', $headerTextStyle);
$table->addCell(3000, $headerCellStyle)->addText('${total_final}', $headerTextStyle);
$section->addTextBreak(1);

$section->addText('MODALIDAD: ${modalidad}');
$section->addText('FECHA IDEAL DE SERVICIO: ${fecha_ideal}');

$section2 = $phpWord->addSection();
$section2->addText('CONTRATO No. ${folio}', ['bold' => true, 'size' => 12, 'color' => '1B3A6B']);
$section2->addTextBreak(1);
$section2->addText(
    'CONTRATO DE PRESTACIÓN DE SERVICIOS DE AUTOTRANSPORTE DE CARGA QUE CELEBRAN POR UNA PARTE ${proveedor_nombre} COMO “EL PROVEEDOR” Y POR LA OTRA PARTE COMO “EL CONSUMIDOR”, CUYO NOMBRE, ${cliente_nombre}, Y DATOS CONSTAN EN LA CARÁTULA DE ESTE CONTRATO COMO PARTE INTEGRAL DEL MISMO, SUJETÁNDOSE AL TENOR DE LAS SIGUIENTES:'
);
$section2->addTextBreak(1);
$section2->addText('C L Á U S U L A S', ['bold' => true, 'color' => '1B3A6B']);
$section2->addTextBreak(1);

$section2->addText('PRIMERA.- El objeto del presente contrato es la prestación del servicio de Transporte de Carga, el cual podrá ser local o foráneo, según se determine en la carátula.');
$section2->addText('SEGUNDA.- EL PROVEEDOR se obliga a entregar a EL CONSUMIDOR una copia del presente contrato de adhesión al momento de su firma.');
$section2->addText('TERCERA.- Es obligación de EL PROVEEDOR que el personal que se encargará de las maniobras de carga y descarga de los bienes se identifique plenamente ante EL CONSUMIDOR, antes de iniciar las operaciones contratadas y especifique las maniobras junto con el inventario que viene señalado en el contrato. TODO SERVICIO ADICIONAL NO CONSIDERADO EN EL INVENTARIO Y DESCRIPCIÓN DEL SERVICIO CAUSARÁ UN CARGO ADICIONAL SUJETO AL ENCARGADO DE MANIOBRAS O AL DEPARTAMENTO DE LOGÍSTICA DE LA EMPRESA.');
$section2->addText('CUARTA.- Es obligación de EL CONSUMIDOR declarar verazmente al proveedor toda la información relativa a la descripción, valor, cantidad, peso y demás características de la mercancía que pretende transportar; en caso de falsedad, EL CONSUMIDOR asumirá la responsabilidad respectiva y las repercusiones en costos adicionales.');
$section2->addText('QUINTA.- Es obligación de EL PROVEEDOR entregar por escrito la Carta de Garantías respectiva a la prestación del servicio. En caso de alguna eventualidad o daño en el tipo de servicio EXCLUSIVO, la empresa solo se hace responsable de cubrir dicho imprevisto con el 5% al 15% del daño del mueble, tomando en cuenta el estado en el que se encuentra dicho objeto (menaje de casa en uso); solo aplica al siguiente día después de la entrega de su mercancía y es responsabilidad del cliente la contratación de un seguro para su carga (cubre trayecto de origen y destino y solo cubre robo o siniestro).');
$section2->addText('SEXTA.- En caso de algún daño en los bienes del consumidor en el tipo de servicio COMPARTIDO, la empresa solo aplica un reembolso del 1% al 10% tomando en cuenta el daño generado a los bienes y es responsabilidad del cliente la contratación de un seguro para su carga (cubre trayecto de origen y destino y solo cubre robo o siniestro).');
$section2->addText('SÉPTIMA.- La empresa no se hace responsable del funcionamiento de electrónica, línea blanca y electrodomésticos. Solo se hace responsable en caso de golpes visibles causados en las maniobras. *Las plantas y cristales viajan por cuenta y riesgo del cliente; ninguna ruptura, abolladura o despostillamiento de algún ónix, piedra, mármol, etc., NO se pagará, ya que estos por su naturaleza son susceptibles a romperse.');
$section2->addText('OCTAVA.- Se establece como penalización por incumplimiento de contrato para cualquiera de las partes el 15% del valor total de la operación.');
$section2->addText('NOVENA.- Es responsabilidad del CLIENTE revisar el tipo de maniobras (volados, acarreos, etc.) y servicios que incluyen nuestros vendedores antes de hacer el primer depósito a cuenta de sus servicios, para evitar situaciones de conflicto.');
$section2->addText('DÉCIMA.- El cliente enviará por correo o mediante un mensaje de texto el comprobante del 10% Y el 60% cuando esté el producto en la unidad Y EL 30% antes de entregar en destino.');
$section2->addText('DÉCIMA PRIMERA.- La Procuraduría Federal del Consumidor es competente en la vía administrativa para resolver controversia que se suscite sobre la interpretación o cumplimiento del presente contrato; sin perjuicio de lo anterior, las partes se someten a la jurisdicción de los tribunales competentes en la Ciudad de México, renunciando expresamente a cualquier otra jurisdicción que pudiera corresponderles por razón de sus domicilios presentes o futuros o por cualquier otra razón.');

$section2->addTextBreak(1);
$section2->addText('Leído el contenido del presente contrato por parte de los interesados y sabedoras de su alcance legal, lo firman por duplicado en la Ciudad ${ciudad_origen} a los ${fecha_actual}.');
$section2->addTextBreak(2);
$section2->addText('JAVIER ASCENCIO MÁRQUEZ', ['bold' => true]);
$section2->addText('PROVEEDOR');

$dest = __DIR__ . '/../resources/templates/carta_porte_template.docx';
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($dest);

if (!file_exists($dest) || filesize($dest) <= 0) {
    fwrite(STDERR, "No se pudo generar la plantilla\n");
    exit(1);
}

fwrite(STDOUT, "Plantilla generada: {$dest}\n");
