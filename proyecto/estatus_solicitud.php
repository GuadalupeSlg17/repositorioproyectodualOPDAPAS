<?php
/**
 * EstatusSolicitud.php
 * Modelo para gestionar el estatus de solicitudes (Sistema de Semáforos)
 */

class EstatusSolicitud
{
    private PDO $db;

    // Configuración de colores del semáforo
    public const COLORES = [
        'pendiente'   => ['hex' => '#F59E0B', 'nombre' => 'Amarillo', 'clase' => 'status-pendiente'],
        'en_proceso'  => ['hex' => '#3B82F6', 'nombre' => 'Azul',     'clase' => 'status-proceso'],
        'aprobado'    => ['hex' => '#10B981', 'nombre' => 'Verde',    'clase' => 'status-aprobado'],
        'no_aprobado' => ['hex' => '#EF4444', 'nombre' => 'Rojo',     'clase' => 'status-rechazado'],
        'terminado'   => ['hex' => '#6B7280', 'nombre' => 'Gris',     'clase' => 'status-terminado'],
    ];

    public function __construct(PDO $database)
    {
        $this->db = $database;
    }

    /**
     * Obtener el estatus de un folio específico
     */
    public function obtenerPorFolio(string $folio): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM estatus_solicitud WHERE folio = :folio LIMIT 1"
        );
        $stmt->execute([':folio' => $folio]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return $this->enriquecerConColor($row);
    }

    /**
     * Listar todos los estatus (con filtros opcionales)
     */
    public function listar(?string $estatus = null, ?string $folio = null): array
    {
        $sql = "SELECT * FROM estatus_solicitud WHERE 1=1";
        $params = [];

        if ($estatus) {
            $sql .= " AND estatus = :estatus";
            $params[':estatus'] = $estatus;
        }
        if ($folio) {
            $sql .= " AND folio LIKE :folio";
            $params[':folio'] = "%$folio%";
        }

        $sql .= " ORDER BY fecha_actualizacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'enriquecerConColor'], $rows);
    }

    /**
     * Registrar un nuevo estatus (al crear solicitud)
     */
    public function registrar(string $folio, string $usuario = 'sistema'): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO estatus_solicitud (folio, estatus, comentario, usuario_actualiza)
             VALUES (:folio, 'pendiente', 'Solicitud registrada', :usuario)"
        );
        return $stmt->execute([
            ':folio'   => $folio,
            ':usuario' => $usuario
        ]);
    }

    /**
     * Actualizar el estatus de una solicitud
     */
    public function actualizar(
        string $folio,
        string $nuevoEstatus,
        string $comentario = '',
        string $usuario = 'sistema'
    ): array {
        // Validar estatus permitido
        $permitidos = array_keys(self::COLORES);
        if (!in_array($nuevoEstatus, $permitidos, true)) {
            return ['ok' => false, 'error' => 'Estatus no válido'];
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE estatus_solicitud 
                 SET estatus = :estatus, 
                     comentario = :comentario, 
                     usuario_actualiza = :usuario 
                 WHERE folio = :folio"
            );
            $stmt->execute([
                ':estatus'    => $nuevoEstatus,
                ':comentario' => $comentario,
                ':usuario'    => $usuario,
                ':folio'      => $folio
            ]);

            if ($stmt->rowCount() === 0) {
                return ['ok' => false, 'error' => 'Folio no encontrado o sin cambios'];
            }

            return ['ok' => true, 'mensaje' => 'Estatus actualizado correctamente'];

        } catch (PDOException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener resumen del semáforo (conteo por estatus)
     */
    public function resumenSemaforo(): array
    {
        $stmt = $this->db->query("SELECT * FROM resumen_semaforo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Agrega información de color al registro
     */
    private function enriquecerConColor(array $row): array
    {
        $estatus = $row['estatus'];
        $row['color'] = self::COLORES[$estatus] ?? ['hex' => '#9CA3AF', 'nombre' => 'Desconocido', 'clase' => ''];
        return $row;
    }
}