const mapaCoordenadas = {
    "Sala principal 1.1 (AP)": { x: 220, y: 260 },
    "Sala principal 1.2":      { x: 200, y: 150 },
    "Banheiro":                { x: 260, y: 60  },
    "Sala da Prensa":          { x: 150, y: 60  },
    "Estoque 1.1":             { x: 480, y: 340 },
    "Estoque 1.2":             { x: 480, y: 150 },
    "Cozinha":                 { x: 410, y: 60  },
    "Banheiro 2":              { x: 500, y: 60  }
};

const acumulador = {};

dadosDoBanco.forEach(registo => {
    const comodo = registo.comodo;
    const vel    = parseFloat(registo.velocidade_5ghz);

    if (!acumulador[comodo]) {
        acumulador[comodo] = { soma: 0, count: 0 };
    }
    acumulador[comodo].soma  += vel;
    acumulador[comodo].count += 1;
});

const labelsGrafico  = [];
const valoresGrafico = [];
const pontosCalor    = [];

Object.keys(acumulador).reverse().forEach(comodo => {
    const media = acumulador[comodo].soma / acumulador[comodo].count;

    labelsGrafico.push(comodo);
    valoresGrafico.push(parseFloat(media.toFixed(2)));

    if (mapaCoordenadas[comodo]) {
        let intensidade = media / 500;
        if (intensidade > 1) intensidade = 1;
        const coords = mapaCoordenadas[comodo];
        pontosCalor.push([coords.x, coords.y, intensidade]);
    }
});

const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
new Chart(ctxBarras, {
    type: 'bar',
    data: {
        labels: labelsGrafico,
        datasets: [{
            label: 'Média de Download (Mbps)',
            data: valoresGrafico,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor:     'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Mbps' } }
        }
    }
});

const canvasCalor   = document.getElementById('mapaCalor');
const mapaContainer = canvasCalor.parentElement;

// Converte Mbps em intensidade (0.0 a 1.0) respeitando as faixas
function mbpsParaIntensidade(mbps) {
    if (mbps > 200) {
        return 0.75 + Math.min((mbps - 200) / 300, 1) * 0.25;
    } else if (mbps > 50) {
        return 0.4 + ((mbps - 50) / 150) * 0.35;
    } else {
        return (mbps / 50) * 0.4;
    }
}

const pontosCalor2 = [];
Object.keys(acumulador).forEach(comodo => {
    const media = acumulador[comodo].soma / acumulador[comodo].count;
    if (mapaCoordenadas[comodo]) {
        const coords = mapaCoordenadas[comodo];
        pontosCalor2.push([coords.x, coords.y, mbpsParaIntensidade(media)]);
    }
});

function ajustarCanvas() {
    canvasCalor.width  = mapaContainer.offsetWidth;
    canvasCalor.height = mapaContainer.offsetHeight;
}
ajustarCanvas();
window.addEventListener('resize', () => {
    ajustarCanvas();
    desenharCalor();
});

function desenharCalor() {
    const escalaX = canvasCalor.width  / 600;
    const escalaY = canvasCalor.height / 400;

    const pontosEscalados = pontosCalor2.map(p => [
        p[0] * escalaX,
        p[1] * escalaY,
        p[2]
    ]);

    const heat = simpleheat('mapaCalor');

    heat.gradient({
        0.0:  '#0000ff',   // azul    — sinal muito fraco
        0.4:  '#00cc44',   // verde   — sinal fraco
        0.7:  '#ffaa00',   // laranja — sinal médio
        1.0:  '#ff0000'    // vermelho — sinal forte
    });

    heat.data(pontosEscalados);
    heat.radius(60 * escalaX, 40 * escalaX);
    heat.max(1.0);
    heat.draw(0.05);
}

desenharCalor();

(function criarLegenda() {
    const legenda = document.createElement('div');
    legenda.style.cssText = `
        display: flex; gap: 18px; justify-content: center;
        margin-top: 8px; font-size: 13px; font-family: 'Segoe UI', sans-serif;
    `;
    legenda.innerHTML = `
        <span><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#ff0000;vertical-align:middle;margin-right:4px;"></span>Forte (&gt; 200 Mbps)</span>
        <span><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#ffaa00;vertical-align:middle;margin-right:4px;"></span>Médio (51–200 Mbps)</span>
        <span><span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#00cc44;vertical-align:middle;margin-right:4px;"></span>Fraco (0–50 Mbps)</span>
    `;
    mapaContainer.parentElement.appendChild(legenda);
})();

document.getElementById('btnIniciarTeste').addEventListener('click', function () {
    const download = (Math.random() * (480 - 10) + 10).toFixed(2);
    const upload   = (Math.random() * (90  - 5)  + 5 ).toFixed(2);
    const ping     = Math.floor(Math.random() * (40 - 4) + 4);

    document.getElementById('lblDownload').innerText = download;
    document.getElementById('lblUpload').innerText   = upload;
    document.getElementById('lblPing').innerText     = ping;

    document.getElementById('hidDownload').value = download;

    document.getElementById('btnSalvarResultado').removeAttribute('disabled');
});

function abrirModalEditar(id, comodo, velocidade) {
    document.getElementById('editId').value         = id;
    document.getElementById('editVelocidade').value = velocidade;

    const selectComodo = document.getElementById('editComodo');
    for (let i = 0; i < selectComodo.options.length; i++) {
        if (selectComodo.options[i].value === comodo) {
            selectComodo.selectedIndex = i;
            break;
        }
    }

    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}