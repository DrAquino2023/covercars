# Migración de archivos a nueva estructura
# Ejecutar desde la raíz del proyecto:  .\migrar.ps1

# Crear alias para copiar con sobreescritura
function Copy-File($src, $dst) {
    $dir = Split-Path $dst
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Force -Path $dir | Out-Null
    }
    Copy-Item -Path $src -Destination $dst -Force
    Write-Host "Copiado: $src -> $dst"
}

# -------------------
# Páginas principales
# -------------------
Copy-File "index.php"                    "public/index.php"
Copy-File "productos.php"                "public/pages/products/index.php"
Copy-File "producto.php"                 "public/pages/products/single.php"
Copy-File "carrito.php"                  "public/pages/cart/index.php"
Copy-File "checkout.php"                 "public/pages/checkout/index.php"

# Sistema de usuarios
Copy-File "perfil.php"                   "public/pages/account/profile.php"
Copy-File "editar_perfil.php"            "public/pages/account/edit.php"
Copy-File "historial.php"                "public/pages/account/history.php"
Copy-File "recuperar_contrasena.php"     "public/pages/auth/reset-password.php"
Copy-File "logout.php"                   "public/includes/auth/logout.php"

# Procesos y formularios
Copy-File "procesar_login.php"           "public/includes/auth/process-login.php"
Copy-File "procesar_registro.php"        "public/includes/auth/process-register.php"
Copy-File "procesar_editar_perfil.php"   "public/includes/account/process-profile.php"
Copy-File "enviar.php"                   "public/includes/contact/send.php"

# Sistema de pagos
Copy-File "generar_pago.php"             "public/includes/payments/generate.php"
Copy-File "pago_exitoso.php"             "public/pages/checkout/success.php"
Copy-File "pago_pendiente.php"           "public/pages/checkout/pending.php"
Copy-File "pago_fallido.php"             "public/pages/checkout/failed.php"
Copy-File "mp_webhook.php"               "api/endpoints/payments/webhook.php"

# Dashboard y base
Copy-File "admin/index.php"              "admin/dashboard/index.php"
Copy-File "admin/layout.php"             "admin/includes/layout.php"
Copy-File "admin/footer_layout.php"      "admin/includes/footer.php"

# Módulos principales
Copy-File "admin/productos.php"          "admin/modules/products/index.php"
Copy-File "admin/producto_form.php"      "admin/modules/products/form.php"
Copy-File "admin/pedidos.php"            "admin/modules/orders/index.php"
Copy-File "admin/order_detail.php"       "admin/modules/orders/detail.php"
Copy-File "admin/usuarios.php"           "admin/modules/users/index.php"
Copy-File "admin/usuario_form.php"       "admin/modules/users/form.php"
Copy-File "admin/reportes.php"           "admin/modules/analytics/reports.php"

# Assets del admin
Copy-File "admin/custom-admin.css"       "admin/assets/css/custom.css"
Copy-File "admin/js/admin.js"            "admin/assets/js/admin.js"
Copy-File "admin/update_status.php"      "admin/includes/update-status.php"
Copy-File "admin/update-productos-table.sql" "database/migrations/001_update_products.sql"

# JavaScript frontend
Copy-File "js/carrito.js"                "public/assets/js/cart.js"
Copy-File "js/script.js"                 "public/assets/js/app.js"
Copy-File "js/checkout.js"               "public/assets/js/checkout.js"
Copy-File "js/validacion-form.js"        "public/assets/js/validation.js"

# CSS frontend y admin
Copy-File "css\*"                        "public/assets/css\" 
Copy-File "css\*admin*"                  "admin/assets/css\"

# Imágenes
Copy-File "img/products/*"               "shared/uploads/products/"
Copy-File "img/icons/*"                  "public/assets/images/icons/"
Copy-File "img/logos/*"                  "public/assets/images/brand/"

# Componentes reutilizables
Copy-File "componentes/header.php"       "shared/components/public/header.php"
Copy-File "componentes/footer.php"       "shared/components/public/footer.php"
Copy-File "componentes/contacto.php"     "shared/components/public/contact.php"

Write-Host "`nMigración completada ✅"
