<?php
/** Crea la preferencia de Mercado Pago de una factura y redirige al checkout. */
require dirname(__DIR__) . '/app/bootstrap.php';
$client = require_client();

if (!is_post()) {
    redirect('portal/facturas.php');
}
verify_csrf();

$invoice = db_one(
    "SELECT * FROM invoices WHERE id = ? AND client_id = ? AND status IN ('pending','partial')",
    [input_int('id'), (int) $client['id']]
);
if (!$invoice || invoice_balance($invoice) <= 0) {
    flash('warn', 'Esta factura no tiene saldo pendiente.');
    redirect('portal/facturas.php');
}

if (!mp_configured()) {
    flash('warn', 'El pago en línea estará disponible muy pronto. Mientras tanto, escríbenos a ' . setting('company_email') . ' para coordinar el pago de la factura ' . $invoice['number'] . '.');
    redirect('portal/factura.php?id=' . $invoice['id']);
}

try {
    $checkoutUrl = mp_create_preference($invoice, $client);
} catch (Throwable $e) {
    error_log('pagar.php: ' . $e->getMessage());
    flash('error', 'No pudimos iniciar el pago con Mercado Pago. Inténtalo de nuevo en unos minutos.');
    redirect('portal/factura.php?id=' . $invoice['id']);
}

header('Location: ' . $checkoutUrl, true, 303);
exit;
