-- ============================================================
--  FLETESCUN — Esquema de Base de Datos v2
--  Actualizado con los campos reales del cotizador (4 pasos)
--  Motor: MySQL / MariaDB (Laravel + Hostinger)
--
--  CAMBIOS vs v1:
--  · clientes: se agrega `email` (Step1 usa "email", no "correo")
--  · cotizaciones: se unifican tipo_servicio + modalidad,
--    se separan elevador y piso por punto (origen/destino),
--    se añade acceso_estacionamiento_origen/destino,
--    se eliminan campos que no existen en el formulario real
--  · servicios_adicionales: nueva tabla normalizada
--    (embalaje, desmontaje, volado, seguro)
--  · inventario_articulos: se agrega columna `m3` y `es_especial`
--    para diferenciar catálogo vs artículos especiales
--  · Se agrega columna `precio_estimado_min/max` (rango del Step4)
--  · Tabla `aceptaciones_contrato` para los dos checkboxes del Step4
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ============================================================
-- 1. CLIENTES
--    Step1: nombre, telefono, email (correo)
--    El campo se llama "email" en el formulario, se mantiene
--    "correo" en BD pero se documenta el alias.
--    Anti-duplicados: telefono + correo + origen + destino.
-- ============================================================
CREATE TABLE clientes (
    id              CHAR(36)        NOT NULL DEFAULT (UUID()),
    nombre          VARCHAR(120)    NOT NULL         COMMENT 'Step1: formData.nombre',
    telefono        VARCHAR(20)     NOT NULL         COMMENT 'Step1: formData.telefono',
    correo          VARCHAR(120)    NOT NULL         COMMENT 'Step1: formData.email',
    ip_origen       VARCHAR(45)     NULL             COMMENT 'IP de la solicitud, para anti-duplicados',
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_telefono (telefono),
    INDEX idx_correo   (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Datos de contacto capturados en Step1Contact';


-- ============================================================
-- 2. COTIZACIONES (Folios)
--    Combina campos de Step1 (ruta, fecha) y Step2 (logística,
--    modalidad). La columna `tipo_servicio` reemplaza al ENUM
--    de modalidades habitacionales, que ya no existe en el form.
--
--    Campos eliminados vs v1:
--    - `modalidad` ENUM (8 opciones) → no aparece en ningún step
--    - `uso_elevador` genérico → reemplazado por dos booleanos
--      específicos: elevador_origen / elevador_destino
--
--    Campos agregados vs v1:
--    - direccion_origen / direccion_destino (Step1: formData.origen/destino)
--    - piso_origen / piso_destino (Step2: formData.pisoOrigen/pisoDestino)
--    - elevador_origen / elevador_destino (Step2: formData.elevadorOrigen/Destino)
--    - acceso_estacionamiento_origen / destino (Step1: 'si'/'no'/<40m/>40m)
--    - precio_estimado_min / precio_estimado_max (rango calculado en Step4)
--    - precio_estimado_modo (modalidad al momento del cálculo)
-- ============================================================
CREATE TABLE cotizaciones (
    id                              CHAR(36)        NOT NULL DEFAULT (UUID()),
    folio                           VARCHAR(30)     NOT NULL COMMENT 'Ej: 00212401-XYZ, único y encriptado',
    cliente_id                      CHAR(36)        NOT NULL,

    -- ── Step 1: Ruta ──────────────────────────────────────────
    direccion_origen                VARCHAR(255)    NOT NULL COMMENT 'Step1: formData.origen (calle + ciudad)',
    direccion_destino               VARCHAR(255)    NOT NULL COMMENT 'Step1: formData.destino (calle + ciudad)',
    fecha_ideal                     DATE            NULL     COMMENT 'Step1: formData.fecha',

    -- ── Step 1: Acceso de estacionamiento ────────────────────
    -- Valor 'si' = camión puede estacionar a <40m
    -- Valor 'no' = distancia >40m, puede implicar acarreo
    acceso_estacionamiento_origen   ENUM('si','no') NULL     COMMENT 'Step1: formData.accesoEstacionamientoOrigen',
    acceso_estacionamiento_destino  ENUM('si','no') NULL     COMMENT 'Step1: formData.accesoEstacionamientoDestino',

    -- ── Step 2: Logística de pisos ────────────────────────────
    piso_origen                     VARCHAR(30)     NULL     COMMENT 'Step2: formData.pisoOrigen (ej. "2do piso")',
    piso_destino                    VARCHAR(30)     NULL     COMMENT 'Step2: formData.pisoDestino',
    elevador_origen                 TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Step2: formData.elevadorOrigen',
    elevador_destino                TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Step2: formData.elevadorDestino',

    -- ── Step 2: Modalidad de servicio ─────────────────────────
    tipo_servicio                   ENUM('Exclusivo','Compartido') NOT NULL DEFAULT 'Exclusivo'
                                    COMMENT 'Step2: formData.modalidad ("exclusivo"/"compartido")',

    -- ── Financiero (calculado en Step4, ajustado por el jefe) ─
    distancia_km                    DECIMAL(10,2)   NULL     COMMENT 'Calculada por API de Google Maps',
    precio_estimado_min             DECIMAL(10,2)   NULL     COMMENT 'Step4: price.min (rango automático)',
    precio_estimado_max             DECIMAL(10,2)   NULL     COMMENT 'Step4: price.max (rango automático)',
    precio_final                    DECIMAL(10,2)   NULL     COMMENT 'Ajustado por el jefe tras revisar fotos',
    moneda                          CHAR(3)         NOT NULL DEFAULT 'MXN',

    -- ── Máquina de estados ────────────────────────────────────
    estatus                         ENUM(
                                        'Prospecto',
                                        'Confirmado',
                                        'En Tránsito Seguro',
                                        'Descarga Autorizada',
                                        'Finalizado',
                                        'Cancelado',
                                        'Expirado'
                                    ) NOT NULL DEFAULT 'Prospecto',

    -- ── Flags de control ──────────────────────────────────────
    es_duplicado_alerta             TINYINT(1)      NOT NULL DEFAULT 0
                                    COMMENT '1 = el jefe fue alertado de posible duplicado',
    portal_activo                   TINYINT(1)      NOT NULL DEFAULT 1
                                    COMMENT '0 = acceso del cliente al mini-portal deshabilitado',

    -- ── Timestamps clave ──────────────────────────────────────
    creado_en                       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    confirmado_en                   TIMESTAMP       NULL,
    finalizado_en                   TIMESTAMP       NULL,
    cancelado_en                    TIMESTAMP       NULL,
    expirado_en                     TIMESTAMP       NULL,
    actualizado_en                  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uk_folio                (folio),
    INDEX   idx_cliente                 (cliente_id),
    INDEX   idx_estatus                 (estatus),
    INDEX   idx_tipo_servicio           (tipo_servicio),
    CONSTRAINT fk_cot_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Folio del servicio: núcleo del sistema (Step1 + Step2 + Step4)';


-- ============================================================
-- 3. SERVICIOS ADICIONALES
--    Step2: formData.serviciosAdicionalesEmbalaje / Desmontaje /
--           Volado / Seguro
--
--    Diseño: tabla normalizada 1-a-muchos en lugar de 4 columnas
--    booleanas en cotizaciones. Así se pueden agregar más servicios
--    al catálogo sin alterar el esquema.
--
--    ENUM de servicio refleja exactamente las 4 opciones del Step2.
-- ============================================================
CREATE TABLE servicios_adicionales (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    servicio        ENUM(
                        'Embalaje de cajas',
                        'Desmontaje de muebles',
                        'Volado / acarreo externo',
                        'Seguro de carga'
                    ) NOT NULL                   COMMENT 'Step2: nombre del servicio adicional seleccionado',
    PRIMARY KEY (id),
    UNIQUE  KEY uk_cotizacion_servicio (cotizacion_id, servicio) COMMENT 'Un servicio no se repite por folio',
    CONSTRAINT fk_serv_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Servicios adicionales seleccionados en Step2 (embalaje, desmontaje, volado, seguro)';


-- ============================================================
-- 4. HISTORIAL DE ESTADOS
--    Auditoría de cada transición de la máquina de estados.
--    Alimenta el embudo de ventas del CMS.
-- ============================================================
CREATE TABLE historial_estados (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    estado_anterior VARCHAR(30)     NULL     COMMENT 'NULL en la inserción inicial',
    estado_nuevo    VARCHAR(30)     NOT NULL,
    motivo          TEXT            NULL     COMMENT 'Razón de cancelación, rechazo, etc.',
    generado_por    ENUM(
                        'Sistema',
                        'Administrador',
                        'Cron'
                    ) NOT NULL DEFAULT 'Sistema',
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_hist_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Auditoría de cambios de estado: fuente del embudo de ventas del CMS';


-- ============================================================
-- 5. INVENTARIO DE ARTÍCULOS
--    Step3: catálogo visual (InventoryItem) + artículos especiales
--
--    Cambios vs v1:
--    - `m3` DECIMAL: volumen del catálogo (ej. "2.5 m³"),
--      que en el frontend se guarda como observaciones.
--      Se separa a su propia columna para poder sumar volúmenes.
--    - `es_especial` TINYINT: 0 = del catálogo, 1 = artículo
--      especial (libre, frágil, no listado). Diferencia las dos
--      secciones del Step3.
--    - `fragil` TINYINT: relevante para especiales (la columna
--      "Observaciones" del formulario de artículos especiales
--      puede indicar "Frágil").
--    - `observaciones` VARCHAR: Step3 campo libre de artículos
--      especiales ("Frágil, requiere cuidado, no asegurado...").
--      Para artículos del catálogo almacena la cadena "{m3} m³"
--      que genera el frontend.
--    - `categoria` VARCHAR: categoría del catálogo (ej. "cocina",
--      "sofas") para agrupar en la Carta Porte.
-- ============================================================
CREATE TABLE inventario_articulos (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    nombre          VARCHAR(120)    NOT NULL         COMMENT 'Step3: item.articulo',
    cantidad        SMALLINT        NOT NULL DEFAULT 1 COMMENT 'Step3: item.cantidad',
    m3              DECIMAL(6,3)    NULL             COMMENT 'Step3: item.m3 (del catálogo visual)',
    categoria       VARCHAR(40)     NULL             COMMENT 'Step3: categoría del catálogo (ej. "cocina")',
    es_especial     TINYINT(1)      NOT NULL DEFAULT 0
                                    COMMENT '0=catálogo visual, 1=artículo especial libre',
    fragil          TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Derivado de observaciones del artículo especial',
    observaciones   VARCHAR(255)    NULL             COMMENT 'Step3: item.observaciones (campo libre)',
    orden           SMALLINT        NOT NULL DEFAULT 0 COMMENT 'Orden en la tabla de la Carta Porte Word',
    PRIMARY KEY (id),
    INDEX idx_cotizacion    (cotizacion_id),
    INDEX idx_es_especial   (es_especial),
    CONSTRAINT fk_inv_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Artículos del catálogo y especiales declarados en Step3Inventory';


-- ============================================================
-- 6. FOTOS DEL ANEXO VISUAL
--    Step3: formData.fotos (array de URLs temporales / blobs)
--    El servidor renombra y comprime cada archivo antes de
--    guardarlo. Se relacionan opcionalmente a un artículo.
-- ============================================================
CREATE TABLE fotos_anexo (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    articulo_id     BIGINT UNSIGNED NULL             COMMENT 'FK opcional al artículo al que pertenece',
    nombre_archivo  VARCHAR(255)    NOT NULL         COMMENT 'Nombre seguro renombrado: FolioXXX_FotoN.jpg',
    ruta_relativa   VARCHAR(500)    NOT NULL         COMMENT 'Ruta en disco relativa a storage/',
    tipo_mime       VARCHAR(50)     NOT NULL         COMMENT 'image/jpeg, image/png, image/heic...',
    tamanio_bytes   INT UNSIGNED    NOT NULL,
    orden           SMALLINT        NOT NULL DEFAULT 0 COMMENT 'Posición en cuadrícula del PDF',
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_foto_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_foto_articulo
        FOREIGN KEY (articulo_id) REFERENCES inventario_articulos (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Fotos subidas en Step3; base del Anexo Fotográfico PDF';


-- ============================================================
-- 7. ACEPTACIONES DE CONTRATO
--    Step4: los dos checkboxes de aceptación del contrato.
--    Se guarda como registro propio por trazabilidad legal:
--    - acceptTerms  → "He leído y acepto todos los términos..."
--    - acceptInventory → "Declaro que el inventario es veraz..."
--    Se registra IP y timestamp exacto para auditoría.
-- ============================================================
CREATE TABLE aceptaciones_contrato (
    id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id           CHAR(36)        NOT NULL,
    acepta_terminos         TINYINT(1)      NOT NULL DEFAULT 0
                                            COMMENT 'Step4: acceptTerms (términos y cláusulas)',
    acepta_inventario       TINYINT(1)      NOT NULL DEFAULT 0
                                            COMMENT 'Step4: acceptInventory (veracidad del inventario)',
    ip                      VARCHAR(45)     NULL,
    user_agent              TEXT            NULL,
    aceptado_en             TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_acept_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de aceptación de los dos checkboxes del Step4 (trazabilidad legal)';


-- ============================================================
-- 8. DOCUMENTOS GENERADOS
--    Carta Porte (Word) y Anexo Fotográfico (PDF) generados
--    al finalizar el Step4 (onGenerate).
-- ============================================================
CREATE TABLE documentos_generados (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    tipo            ENUM(
                        'Carta Porte Word',
                        'Anexo Fotográfico PDF'
                    ) NOT NULL,
    nombre_archivo  VARCHAR(255)    NOT NULL,
    ruta_relativa   VARCHAR(500)    NOT NULL,
    tamanio_bytes   INT UNSIGNED    NOT NULL,
    enviado_al_jefe TINYINT(1)      NOT NULL DEFAULT 0,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_doc_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Documentos Word/PDF generados al hacer clic en "Generar contrato formal"';


-- ============================================================
-- 9. FASES DE PAGO
--    Esquema 10%–60%–30% que el Step4 ya muestra al cliente.
--    Se precargan 3 filas automáticamente al crear la cotización.
-- ============================================================
CREATE TABLE fases_pago (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    fase            TINYINT         NOT NULL COMMENT '1=Anticipo (10%), 2=Maniobra (60%), 3=Liquidación (30%)',
    porcentaje      DECIMAL(5,2)    NOT NULL COMMENT '10.00 / 60.00 / 30.00',
    monto_esperado  DECIMAL(10,2)   NULL     COMMENT 'Calculado al fijar precio_final',
    estatus         ENUM(
                        'Bloqueado',
                        'Pendiente',
                        'En Revisión',
                        'Aprobado',
                        'Rechazado'
                    ) NOT NULL DEFAULT 'Bloqueado',
    desbloqueado_en TIMESTAMP       NULL,
    PRIMARY KEY (id),
    UNIQUE  KEY uk_cotizacion_fase (cotizacion_id, fase),
    CONSTRAINT fk_fase_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Las 3 fases financieras del servicio (10%–60%–30%) mostradas en Step4';


-- ============================================================
-- 10. COMPROBANTES DE PAGO
--     Archivos subidos por el cliente por cada fase.
--     El administrador los aprueba o rechaza desde el CMS.
-- ============================================================
CREATE TABLE comprobantes_pago (
    id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fase_pago_id        BIGINT UNSIGNED NOT NULL,
    cotizacion_id       CHAR(36)        NOT NULL COMMENT 'Desnormalizado para queries rápidos',
    nombre_archivo      VARCHAR(255)    NOT NULL COMMENT 'Ej: Folio00212401_Pago1.jpg',
    ruta_relativa       VARCHAR(500)    NOT NULL,
    tipo_mime           VARCHAR(50)     NOT NULL,
    tamanio_bytes       INT UNSIGNED    NOT NULL,
    estatus_revision    ENUM(
                            'En Revisión',
                            'Aprobado',
                            'Rechazado'
                        ) NOT NULL DEFAULT 'En Revisión',
    motivo_rechazo      TEXT            NULL,
    revisado_en         TIMESTAMP       NULL,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_fase       (fase_pago_id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_comp_fase
        FOREIGN KEY (fase_pago_id) REFERENCES fases_pago (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Comprobantes de pago subidos por el cliente en el mini-portal';


-- ============================================================
-- 11. TOKENS DE ACCESO AL PORTAL (OTP)
--     Código de 6 dígitos con vigencia de 10 minutos para
--     la autenticación del mini-portal.
-- ============================================================
CREATE TABLE tokens_acceso_portal (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    codigo_hash     VARCHAR(255)    NOT NULL COMMENT 'Hash bcrypt del código de 6 dígitos',
    intentos        TINYINT         NOT NULL DEFAULT 0,
    expira_en       TIMESTAMP       NOT NULL COMMENT 'Vigencia máxima: 10 minutos',
    usado           TINYINT(1)      NOT NULL DEFAULT 0,
    ip_solicitud    VARCHAR(45)     NULL,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_token_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='OTPs de 6 dígitos para acceso al mini-portal (vigencia 10 min)';


-- ============================================================
-- 12. SESIONES DE PORTAL
--     Sesión activa del cliente una vez validado el OTP.
-- ============================================================
CREATE TABLE sesiones_portal (
    id              CHAR(36)        NOT NULL DEFAULT (UUID()),
    cotizacion_id   CHAR(36)        NOT NULL,
    token_hash      VARCHAR(255)    NOT NULL COMMENT 'Token de sesión hasheado',
    ip              VARCHAR(45)     NULL,
    user_agent      TEXT            NULL,
    expira_en       TIMESTAMP       NOT NULL,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    CONSTRAINT fk_sesion_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sesión activa del cliente en el mini-portal tras validar el OTP';


-- ============================================================
-- 13. RESEÑAS
--     Captadas al finalizar el servicio (Módulo 6).
--     Una sola reseña por folio. El jefe las modera en el CMS.
-- ============================================================
CREATE TABLE resenas (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NOT NULL,
    cliente_id      CHAR(36)        NOT NULL,
    calificacion    TINYINT         NOT NULL COMMENT '1 a 5 estrellas',
    comentario      TEXT            NULL,
    estatus         ENUM(
                        'Pendiente',
                        'Publicada',
                        'Rechazada'
                    ) NOT NULL DEFAULT 'Pendiente',
    moderado_en     TIMESTAMP       NULL,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uk_cotizacion_resena (cotizacion_id) COMMENT 'Una sola reseña por folio',
    INDEX   idx_cliente              (cliente_id),
    INDEX   idx_estatus              (estatus),
    CONSTRAINT fk_resena_cotizacion
        FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_resena_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_calificacion CHECK (calificacion BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Reseñas validadas por folio con moderación CMS';


-- ============================================================
-- 14. NOTIFICACIONES DE CORREO (log PHPMailer)
-- ============================================================
CREATE TABLE notificaciones_correo (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cotizacion_id   CHAR(36)        NULL,
    destinatario    VARCHAR(120)    NOT NULL,
    asunto          VARCHAR(255)    NOT NULL,
    tipo            ENUM(
                        'Nuevo Prospecto',
                        'OTP Acceso',
                        'Comprobante Recibido',
                        'Pago Aprobado',
                        'Pago Rechazado',
                        'Folio Cancelado',
                        'Folio Expirado',
                        'Solicitud Reseña',
                        'Otro'
                    ) NOT NULL DEFAULT 'Otro',
    estatus         ENUM('Enviado','Fallido') NOT NULL DEFAULT 'Enviado',
    error_msg       TEXT            NULL,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cotizacion (cotizacion_id),
    INDEX idx_tipo       (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log auditable de todos los correos enviados por PHPMailer';


-- ============================================================
-- 15. CONTENIDO CMS
--     Textos e imágenes editables del sitio.
-- ============================================================
CREATE TABLE cms_contenido (
    id              SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    seccion         VARCHAR(80)     NOT NULL COMMENT 'Ej: hero_titulo, nosotros_texto',
    tipo            ENUM('Texto','HTML','Imagen','URL') NOT NULL DEFAULT 'Texto',
    valor           LONGTEXT        NULL,
    ruta_imagen     VARCHAR(500)    NULL,
    actualizado_en  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_seccion (seccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Contenido editable del sitio web desde el panel CMS';


-- ============================================================
-- 16. ADMINISTRADORES
-- ============================================================
CREATE TABLE administradores (
    id              SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(120)    NOT NULL,
    correo          VARCHAR(120)    NOT NULL,
    password_hash   VARCHAR(255)    NOT NULL COMMENT 'bcrypt via Laravel Hash::make()',
    activo          TINYINT(1)      NOT NULL DEFAULT 1,
    ultimo_acceso   TIMESTAMP       NULL,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_correo (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del panel CMS (gerencia de FletesCun)';


-- ============================================================
-- 17. LOG CRON JOBS
--     Registro de ejecuciones del Cron que expira prospectos.
-- ============================================================
CREATE TABLE log_cron (
    id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tarea            VARCHAR(80)     NOT NULL COMMENT 'Ej: expirar_prospectos',
    folios_afectados SMALLINT        NOT NULL DEFAULT 0,
    resultado        ENUM('Éxito','Error') NOT NULL DEFAULT 'Éxito',
    detalle          TEXT            NULL,
    ejecutado_en     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_tarea (tarea)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log de ejecuciones del programador de tareas (Cron Jobs de Laravel)';


-- ============================================================
-- VISTA: EMBUDO DE VENTAS
--    Alimenta el tablero de métricas del CMS.
-- ============================================================
CREATE OR REPLACE VIEW vista_embudo_ventas AS
SELECT
    DATE_FORMAT(creado_en, '%Y-%m')     AS mes,
    tipo_servicio,
    COUNT(*)                             AS total_prospectos,
    SUM(estatus = 'Confirmado')          AS confirmados,
    SUM(estatus IN ('En Tránsito Seguro','Descarga Autorizada','Finalizado'))
                                         AS en_proceso_o_finalizados,
    SUM(estatus = 'Cancelado')           AS cancelados,
    SUM(estatus = 'Expirado')            AS expirados,
    ROUND(
        SUM(estatus NOT IN ('Prospecto','Cancelado','Expirado')) * 100.0
        / NULLIF(COUNT(*), 0),
    2)                                   AS tasa_conversion_pct
FROM cotizaciones
GROUP BY mes, tipo_servicio
ORDER BY mes DESC;


-- ============================================================
-- VISTA: ESTADO DE CUENTA POR FOLIO
--    Render del mini-portal del cliente (3 fases + comprobante).
-- ============================================================
CREATE OR REPLACE VIEW vista_estado_cuenta AS
SELECT
    c.folio,
    c.estatus                   AS estatus_servicio,
    c.tipo_servicio,
    c.precio_final,
    c.precio_estimado_min,
    c.precio_estimado_max,
    fp.fase,
    fp.porcentaje,
    fp.monto_esperado,
    fp.estatus                  AS estatus_fase,
    fp.desbloqueado_en,
    comp.estatus_revision,
    comp.creado_en              AS comprobante_subido_en
FROM cotizaciones c
JOIN fases_pago fp ON fp.cotizacion_id = c.id
LEFT JOIN comprobantes_pago comp
       ON comp.fase_pago_id = fp.id
      AND comp.creado_en = (
              SELECT MAX(cp2.creado_en)
              FROM comprobantes_pago cp2
              WHERE cp2.fase_pago_id = fp.id
          )
ORDER BY c.folio, fp.fase;


-- ============================================================
-- VISTA: VOLUMEN TOTAL DECLARADO POR COTIZACIÓN
--    Suma los m³ del catálogo para que el jefe vea el volumen
--    total al revisar el contrato.
-- ============================================================
CREATE OR REPLACE VIEW vista_volumen_cotizacion AS
SELECT
    cotizacion_id,
    SUM(cantidad * COALESCE(m3, 0))     AS m3_total,
    COUNT(*)                             AS total_articulos,
    SUM(es_especial)                     AS articulos_especiales,
    SUM(fragil)                          AS articulos_fragiles
FROM inventario_articulos
GROUP BY cotizacion_id;


SET FOREIGN_KEY_CHECKS = 1;

-- FIN DEL ESQUEMA FLETESCUN v2
