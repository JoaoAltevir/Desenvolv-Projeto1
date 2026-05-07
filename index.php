<?php
require_once 'config/database.php';
require_once 'app/models/TesteWifi.php';
require_once 'app/controllers/TesteController.php';
$controller = new TesteController();

$action = $_GET['action'] ?? 'painel';

switch ($action) {
    case 'salvar':
        $controller->salvarMedicao();
        break;

    case 'deletar':
        $controller->deletarMedicao();
        break;

    case 'atualizar':
        $controller->atualizarMedicao();
        break;

    default:
        $controller->exibirPainel();
        break;
}
?>