<?php
$mensaje = '';
if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1) {
    $mensaje = '<div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;">✅ Solicitud eliminada correctamente.</div>';
}
if (isset($_GET['error']) && $_GET['error'] == 1) {
    $mensaje = '<div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;">❌ Error al eliminar la solicitud.</div>';
}
?>