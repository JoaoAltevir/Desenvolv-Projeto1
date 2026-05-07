<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Monitorização Wi-Fi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <h1 class="mb-4 text-center text-primary">Sistema de Análise Wi-Fi (5GHz)</h1>

    <div class="row">

        <div class="col-xl-5 col-lg-6">

            <div class="card p-4">
                <h4 class="card-title text-success">Teste de Velocidade de Rede</h4>
                <button type="button" class="btn btn-success w-50 mb-3" id="btnIniciarTeste">
                    Iniciar Teste
                </button>

                <h5 class="fw-bold">Resultado Atual:</h5>
                <p class="fs-5 mb-1">Download: <strong id="lblDownload">0.00</strong> Mbps</p>
                <p class="fs-5 mb-1">Upload: <strong id="lblUpload">0.00</strong> Mbps</p>
                <p class="fs-5 mb-3">Ping: <strong id="lblPing">0</strong> ms</p>

                <form action="index.php?action=salvar" method="POST">
                    <div class="mb-3">
                        <label for="comodo" class="form-label text-muted">Selecione o Cômodo:</label>
                        <select name="comodo_selecionado" id="comodo" class="form-select w-75">
                            <option value="Sala principal 1.1 (AP)">Sala principal 1.1 (AP)</option>
                            <option value="Sala principal 1.2">Sala principal 1.2</option>
                            <option value="Sala da Prensa">Sala da Prensa</option>
                            <option value="Banheiro">Banheiro</option>
                            <option value="Estoque 1.1">Estoque 1.1</option>
                            <option value="Estoque 1.2">Estoque 1.2</option>
                            <option value="Cozinha">Cozinha</option>
                            <option value="Banheiro 2">Banheiro 2</option>
                        </select>
                    </div>
                    <input type="hidden" name="download" id="hidDownload" value="0">

                    <button type="submit" class="btn btn-success w-50" id="btnSalvarResultado" disabled>
                        Salvar Resultado
                    </button>
                </form>
            </div>

            <div class="card p-4">
                <h4 class="card-title">Histórico de Testes Salvos (Backend)</h4>
                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Local</th>
                                <th>5GHz (Mbps)</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historico)): ?>
                                <?php foreach ($historico as $registo): ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $registo['id']; ?></td>
                                        <td><?php echo htmlspecialchars($registo['comodo']); ?></td>
                                        <td>
                                            <span class="badge bg-primary fs-6">
                                                <?php echo $registo['velocidade_5ghz']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm text-white"
                                                    onclick="abrirModalEditar(
                                                        <?php echo $registo['id']; ?>,
                                                        '<?php echo htmlspecialchars($registo['comodo'], ENT_QUOTES); ?>',
                                                        <?php echo $registo['velocidade_5ghz']; ?>
                                                    )">
                                                Modificar
                                            </button>

                                            <a href="index.php?action=deletar&id=<?php echo $registo['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Tem certeza que deseja deletar este registo?')">
                                                Deletar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Nenhum teste salvo ainda.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-xl-7 col-lg-6">

            <div class="card p-4">
                <h4 class="card-title">Média de Velocidade por Local</h4>
                <canvas id="graficoBarras" height="130"></canvas>
            </div>

            <div class="card p-4">
                <h4 class="card-title">Distribuição de Sinal sobre a Planta</h4>
                <div class="mapa-container">
                    <div class="planta-bg"></div>
                    <canvas id="mapaCalor"></canvas>
                </div>
            </div>

        </div>

    </div>
</div>


<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Modificar Medição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=atualizar" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">

                    <div class="mb-3">
                        <label for="editComodo" class="form-label">Cômodo</label>
                        <select name="comodo" id="editComodo" class="form-select">
                            <option value="Sala principal 1.1 (AP)">Sala principal 1.1 (AP)</option>
                            <option value="Sala principal 1.2">Sala principal 1.2</option>
                            <option value="Sala da Prensa">Sala da Prensa</option>
                            <option value="Banheiro">Banheiro</option>
                            <option value="Estoque 1.1">Estoque 1.1</option>
                            <option value="Estoque 1.2">Estoque 1.2</option>
                            <option value="Cozinha">Cozinha</option>
                            <option value="Banheiro 2">Banheiro 2</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editVelocidade" class="form-label">Velocidade 5GHz (Mbps)</label>
                        <input type="number" step="0.01" name="velocidade" id="editVelocidade"
                            class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simpleheat/0.4.0/simpleheat.js"></script>

<script>
    const dadosDoBanco = <?php echo json_encode($historico ?? []); ?>;
</script>

<script src="public/js/graficos.js"></script>

</body>
</html>