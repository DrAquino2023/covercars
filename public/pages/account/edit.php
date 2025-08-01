<?php
// =====================================
// editar_perfil.php
// =====================================

require_once 'iniciar_sesion_segura.php';
require_once 'conexion.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$perfil = [];

// Obtener datos del perfil si existen
$stmt = $conn->prepare("SELECT * FROM perfiles WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado && $resultado->num_rows > 0) {
    $perfil = $resultado->fetch_assoc();
}

$modo_lectura = !empty($perfil);

// Incluir encabezado HTML
include 'componentes/header.php';
?>

<main class="container mt-5 mb-5" style="padding-top: 100px;">
    <h2 class="mb-4">Editar Perfil</h2>

    <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Datos del perfil guardados correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <form id="form-editar-perfil" action="procesar_editar_perfil.php" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>"
                    <?= $modo_lectura ? 'readonly' : '' ?>>
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido"
                    value="<?= htmlspecialchars($perfil['apellido'] ?? '') ?>"
                    <?= $modo_lectura ? 'readonly' : '' ?>>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="codigo_pais" class="form-label">Cód. País</label>
                <input type="text" class="form-control" id="codigo_pais" name="codigo_pais"
                    value="<?= htmlspecialchars($perfil['codigo_pais'] ?? '+54') ?>"
                    <?= $modo_lectura ? 'readonly' : '' ?>>
            </div>
            <div class="col-md-8">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono"
                    value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>"
                    <?= $modo_lectura ? 'readonly' : '' ?>>
            </div>
        </div>

        <!-- País, Provincia, Localidad -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="pais" class="form-label">País</label>
                <select id="pais" name="pais_id" class="form-select" <?= $modo_lectura ? 'disabled' : '' ?>>
                    <option value="">Seleccione un país</option>
                    <?php
                    $resultPaises = $conn->query("SELECT id, nombre FROM paises ORDER BY nombre ASC");
                    while ($pais = $resultPaises->fetch_assoc()):
                        $selected = (isset($perfil['pais_id']) && $perfil['pais_id'] == $pais['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $pais['id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($pais['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="provincia" class="form-label">Provincia</label>
                <select id="provincia" name="provincia_id" class="form-select" <?= $modo_lectura ? 'disabled' : '' ?>>
                    <?php if (isset($perfil['provincia_id'])):
                        $prov = $conn->query("SELECT id, nombre FROM provincias WHERE id = " . intval($perfil['provincia_id']))->fetch_assoc(); ?>
                        <option value="<?= $prov['id'] ?>" selected><?= htmlspecialchars($prov['nombre']) ?></option>
                    <?php else: ?>
                        <option value="">Seleccione provincia</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="localidad" class="form-label">Localidad</label>
                <select id="localidad" name="localidad_id" class="form-select" <?= $modo_lectura ? 'disabled' : '' ?>>
                    <?php if (isset($perfil['localidad_id'])):
                        $loc = $conn->query("SELECT id, nombre FROM localidades WHERE id = " . intval($perfil['localidad_id']))->fetch_assoc(); ?>
                        <option value="<?= $loc['id'] ?>" selected><?= htmlspecialchars($loc['nombre']) ?></option>
                    <?php else: ?>
                        <option value="">Seleccione localidad</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text" class="form-control" id="domicilio" name="domicilio"
                value="<?= htmlspecialchars($perfil['domicilio'] ?? '') ?>"
                <?= $modo_lectura ? 'readonly' : '' ?>>
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones del domicilio (opcional)</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                <?= $modo_lectura ? 'readonly' : '' ?>><?= htmlspecialchars($perfil['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="dni" class="form-label">Nro. de Documento</label>
            <input type="text" class="form-control" id="dni" name="dni"
                value="<?= htmlspecialchars($perfil['dni'] ?? '') ?>"
                <?= $modo_lectura ? 'readonly' : '' ?>>
        </div>

        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '') ?>"
                <?= $modo_lectura ? 'readonly' : '' ?>>
        </div>

        <!-- Botones -->
        <div class="d-flex flex-start mt-4">
            <a href="index.php" class="btn btn-secondary me-3">Cancelar</a>
            <?php if ($modo_lectura): ?>
                <button type="button" id="btn-editar" class="btn btn-warning me-3">Editar</button>
                <button type="submit" id="btn-guardar" class="btn btn-success" disabled>Guardar Cambios</button>
            <?php else: ?>
                <button type="submit" class="btn btn-success" id="btn-guardar">Guardar Cambios</button>
            <?php endif; ?>
        </div>
    </form>
</main>

<?php include 'componentes/footer.php'; ?>

<script>
// JavaScript dinámico para provincias/localidades
const paisSelect = document.getElementById('pais');
const provinciaSelect = document.getElementById('provincia');
const localidadSelect = document.getElementById('localidad');

paisSelect?.addEventListener('change', async () => {
    const paisId = paisSelect.value;
    provinciaSelect.innerHTML = '<option value="">Cargando...</option>';
    localidadSelect.innerHTML = '<option value="">Seleccione localidad</option>';

    if (paisId) {
        try {
            const response = await fetch(`obtener_provincias.php?pais_id=${encodeURIComponent(paisId)}`);
            const data = await response.json();
            provinciaSelect.innerHTML = '<option value="">Seleccione provincia</option>';
            data.forEach(prov => {
                provinciaSelect.innerHTML += `<option value="${prov.id}">${prov.nombre}</option>`;
            });
        } catch (error) {
            console.error('Error cargando provincias:', error);
        }
    }
});

provinciaSelect?.addEventListener('change', async () => {
    const provinciaId = provinciaSelect.value;
    localidadSelect.innerHTML = '<option value="">Cargando...</option>';

    if (provinciaId) {
        try {
            const response = await fetch(`obtener_localidades.php?provincia_id=${encodeURIComponent(provinciaId)}`);
            const data = await response.json();
            localidadSelect.innerHTML = '<option value="">Seleccione localidad</option>';
            data.forEach(loc => {
                localidadSelect.innerHTML += `<option value="${loc.id}">${loc.nombre}</option>`;
            });
        } catch (error) {
            console.error('Error cargando localidades:', error);
        }
    }
});

// Botón editar habilita todos los campos
document.getElementById('btn-editar')?.addEventListener('click', () => {
    document.querySelectorAll('#form-editar-perfil input, select, textarea').forEach(el => {
        el.removeAttribute('readonly');
        el.removeAttribute('disabled');
    });
    document.getElementById('btn-guardar').disabled = false;
});
</script>
