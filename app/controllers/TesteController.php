<?php
require_once 'config/database.php';
require_once 'app/models/TesteWifi.php';

class TesteController {

    private function conectar() {
        $database = new Database();
        return $database->getConnection();
    }

    public function exibirPainel() {
        $db          = $this->conectar();
        $testeModel  = new TesteWifi($db);
        $historico   = $testeModel->lerTodos();

        require_once 'app/views/dashboard.php';
    }

    public function salvarMedicao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db         = $this->conectar();
            $testeModel = new TesteWifi($db);

            $comodo     = $_POST['comodo_selecionado'] ?? '';
            $velocidade = $_POST['download']           ?? 0;

            if ($comodo && $velocidade > 0) {
                $testeModel->salvar($comodo, $velocidade);
            }
        }

        header("Location: index.php");
        exit();
    }

    public function atualizarMedicao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db         = $this->conectar();
            $testeModel = new TesteWifi($db);

            $id         = $_POST['id']         ?? 0;
            $comodo     = $_POST['comodo']      ?? '';
            $velocidade = $_POST['velocidade']  ?? 0;

            if ($id && $comodo && $velocidade > 0) {
                $testeModel->atualizar($id, $comodo, $velocidade);
            }
        }

        header("Location: index.php");
        exit();
    }

    public function deletarMedicao() {
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $db         = $this->conectar();
            $testeModel = new TesteWifi($db);
            $testeModel->deletar($id);
        }

        header("Location: index.php");
        exit();
    }
}
?>