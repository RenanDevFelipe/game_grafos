<?php
session_start();

// Evitar cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Função para gerar uma matriz de adjacência aleatória e simétrica
function gerarMatrizAdjacencia($vertices)
{
    $matriz = array_fill(0, $vertices, array_fill(0, $vertices, 0));
    
    // Gera apenas a metade superior e depois copia simetricamente
    for ($i = 0; $i < $vertices; $i++) {
        for ($j = $i + 1; $j < $vertices; $j++) {
            $valor = rand(0, 1);
            $matriz[$i][$j] = $valor;
            $matriz[$j][$i] = $valor; // Simétrica
        }
    }
    
    return $matriz;
}

// Função para transformar a matriz: 0 = bomba (-1), 1 = seguro
function transformarMatrizParaJogo($matriz)
{
    $matrizJogo = [];
    for ($i = 0; $i < count($matriz); $i++) {
        for ($j = 0; $j < count($matriz[0]); $j++) {
            // Onde não há ligação (0), coloca bomba (-1)
            // Onde há ligação (1), coloca seguro (1)
            $matrizJogo[$i][$j] = ($matriz[$i][$j] === 0) ? -1 : 1;
        }
    }
    return $matrizJogo;
}

// Gerar uma matriz de adjacência de 5x5
$matrizAdjacencia = gerarMatrizAdjacencia(5);
$matrizComBombas = transformarMatrizParaJogo($matrizAdjacencia);

// Gerar a versão JSON da matriz para ser passada ao JS
$matrizComBombasJson = json_encode($matrizComBombas, JSON_NUMERIC_CHECK);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo do Grafo - Bandeirinhas e Bombas</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎮 Jogo do Grafo</h1>
            <p>Encontre as células seguras e evite as bombas!</p>
        </div>

        <div class="content">
            <div class="topo-content">
                <div class="lado-esquerdo">
                    <div class="grafo-section">
                        <h2>Grafo Fornecido</h2>
                        <div class="grafo-visual" id="grafoVisual">
                            <svg width="300" height="300" viewBox="0 0 300 300">
                                <!-- Nós do grafo -->
                                <circle cx="150" cy="50" r="20" class="node" id="node-0"></circle>
                                <circle cx="250" cy="120" r="20" class="node" id="node-1"></circle>
                                <circle cx="220" cy="220" r="20" class="node" id="node-2"></circle>
                                <circle cx="80" cy="220" r="20" class="node" id="node-3"></circle>
                                <circle cx="50" cy="120" r="20" class="node" id="node-4"></circle>

                                <!-- Arestas do grafo -->
                                <g id="arestas"></g>

                                <!-- Labels dos nós (visuais) -->
                                <text x="150" y="58" class="node-label">V1</text>
                                <text x="250" y="128" class="node-label">V2</text>
                                <text x="220" y="228" class="node-label">V3</text>
                                <text x="80" y="228" class="node-label">V4</text>
                                <text x="50" y="128" class="node-label">V5</text>
                            </svg>
                        </div>
                        <p class="grafo-info">Estude o grafo e tente encontrar as ligações!</p>
                    </div>

                    <div class="matrix-section" id="matrixSection" style="display: none;">
                        <h2>Matriz de Adjacência do Grafo</h2>
                        <p class="matrix-info">Aqui está a matriz que você precisava descobrir:</p>
                        <div id="matrizContainer" class="matriz-container">
                            <table id="matrizTable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>V1</th>
                                        <th>V2</th>
                                        <th>V3</th>
                                        <th>V4</th>
                                        <th>V5</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($i = 0; $i < 5; $i++) {
                                        echo "<tr>";
                                        echo "<th>V" . ($i + 1) . "</th>";
                                        for ($j = 0; $j < 5; $j++) {
                                            $valor = $matrizAdjacencia[$i][$j];
                                            $classe = $valor == 0 ? 'bomba' : 'segura';
                                            echo "<td class='$classe'>$valor</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="legend">🔴 Vermelho = Sem ligação (Bomba) | 🟢 Verde = Com ligação (Seguro)</p>
                    </div>
                </div>

                <div class="lado-direito">
                    <div class="game-stats">
                        <div class="stat">
                            <span class="stat-label">Células Abertas:</span>
                            <span class="stat-value" id="celulasAbertas">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Status:</span>
                            <span class="stat-value" id="statusJogo">Jogando</span>
                        </div>
                    </div>

                    <div class="tabuleiro-wrapper">
                        <div class="tab-layout" id="tabuleiro">
                            <div class="corner"></div>
                            <?php for ($j = 0; $j < 5; $j++) { echo "<div class='col-label'>V" . ($j + 1) . "</div>"; } ?>

                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                echo "<div class='row-label'>V" . ($i + 1) . "</div>";
                                for ($j = 0; $j < 5; $j++) {
                                    echo "<div class='celula' id='celula-$i-$j' data-linha='$i' data-coluna='$j'>
                                            <div class='celula-front'>?</div>
                                            <div class='celula-back'></div>
                                          </div>";
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <div class="controls">
                        <button id="novoJogo" class="btn btn-primary">Novo Jogo</button>
                    </div>
                </div>
            </div>

            <div class="instrucoes-jogo">
                <h3>📋 Como Montar o Quadro</h3>
                <p>Baseado no grafo fornecido:</p>
                <ul>
                    <li><strong>Linhas:</strong> Representam o vértice de origem (V1, V2, V3, V4, V5)</li>
                    <li><strong>Colunas:</strong> Representam o vértice de destino (V1, V2, V3, V4, V5)</li>
                    <li><strong>Se há uma aresta</strong> entre dois vértices, coloque <strong>1</strong> na matriz</li>
                    <li><strong>Se NÃO há aresta</strong>, coloque <strong>0</strong> (BOMBA!) na matriz</li>
                    <li><strong>A diagonal é sempre 0</strong> (um vértice não se conecta a si mesmo)</li>
                    <li><strong>A matriz é simétrica</strong>: se V0→V1, então V1→V0</li>
                </ul>
                <p class="exemplo"><strong>Exemplo:</strong> Se há uma aresta entre V1 e V2, na matriz isso aparece em <code>matriz[0][1]</code> e <code>matriz[1][0]</code>.</p>
            </div>
        </div>

        <div class="modal" id="modal">
            <div class="modal-content">
                <h2 id="modalTitulo"></h2>
                <p id="modalMensagem"></p>
                <button id="fecharModal" class="btn btn-primary">Jogar Novamente</button>
            </div>
        </div>
    </div>

    <script>
        let matrizComBombas = <?php echo $matrizComBombasJson; ?>;
        let matrizAdjacencia = <?php echo json_encode($matrizAdjacencia, JSON_NUMERIC_CHECK); ?>;
        let celulasReveladas = new Set();
        let jogoFim = false;
        let vitoria = false;
        let totalCelulasSeguras = 0;
        let celulasSeguras = new Set();

        // Posições dos nós no SVG
        const posicoes = {
            0: { x: 150, y: 50 },
            1: { x: 250, y: 120 },
            2: { x: 220, y: 220 },
            3: { x: 80, y: 220 },
            4: { x: 50, y: 120 }
        };

        // Desenhar grafo
        function desenharGrafo() {
            const arestasGroup = document.getElementById('arestas');
            arestasGroup.innerHTML = '';
            
            let arestasDesenhadas = 0;

            for (let i = 0; i < 5; i++) {
                for (let j = i + 1; j < 5; j++) {
                    if (matrizAdjacencia[i][j] === 1) {
                        const x1 = posicoes[i].x;
                        const y1 = posicoes[i].y;
                        const x2 = posicoes[j].x;
                        const y2 = posicoes[j].y;
                        
                        const linha = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        linha.setAttribute('x1', x1);
                        linha.setAttribute('y1', y1);
                        linha.setAttribute('x2', x2);
                        linha.setAttribute('y2', y2);
                        linha.setAttribute('class', 'edge');
                        arestasGroup.appendChild(linha);
                        arestasDesenhadas++;
                    }
                }
            }
        }

        // Contar e mapear células seguras
        function contarCelulasSeguras() {
            let count = 0;
            celulasSeguras.clear();
            for (let i = 0; i < 5; i++) {
                for (let j = 0; j < 5; j++) {
                    if (matrizComBombas[i][j] === 1) {
                        count++;
                        celulasSeguras.add(`${i}-${j}`);
                    }
                }
            }
            return count;
        }

        totalCelulasSeguras = contarCelulasSeguras();

        // Inicializar células
        function inicializarCelulas() {
            const celulas = document.querySelectorAll('.celula');
            celulas.forEach(celula => {
                celula.addEventListener('click', function() {
                    const linha = parseInt(this.dataset.linha);
                    const coluna = parseInt(this.dataset.coluna);
                    revelarCelula(linha, coluna);
                });
                celula.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    const linha = parseInt(this.dataset.linha);
                    const coluna = parseInt(this.dataset.coluna);
                    colocarBandeira(linha, coluna);
                });
            });
        }

        function revelarCelula(linha, coluna) {
            if (jogoFim) return;

            const chave = `${linha}-${coluna}`;
            if (celulasReveladas.has(chave)) return;

            const celula = document.getElementById(`celula-${linha}-${coluna}`);
            celula.classList.add('revelada');
            celulasReveladas.add(chave);

            const valor = matrizComBombas[linha][coluna];

            if (valor === -1) {
                // Bomba!
                celula.innerHTML = '💣';
                celula.classList.add('bomba');
                jogoFim = true;
                vitoria = false;
                mostrarFim('Game Over!', 'Você encontrou uma bomba! 💥');
                revelarTodas();
            } else if (valor === 1) {
                // Célula segura
                celula.innerHTML = '✓';
                celula.classList.add('segura');
                document.getElementById('celulasAbertas').textContent = celulasReveladas.size;

                // Verificar vitória
                if (celulasReveladas.size === totalCelulasSeguras) {
                    jogoFim = true;
                    vitoria = true;
                    mostrarFim('Você Venceu!', '🎉 Parabéns! Encontrou todas as ligações do grafo!');
                }
            }
        }

        function colocarBandeira(linha, coluna) {
            const celula = document.getElementById(`celula-${linha}-${coluna}`);
            if (celula.classList.contains('revelada')) return;
            if (celula.classList.contains('bandeira')) {
                celula.classList.remove('bandeira');
                celula.innerHTML = '?';
            } else {
                celula.classList.add('bandeira');
                celula.innerHTML = '🚩';
            }
        }

        function revelarTodas() {
            for (let i = 0; i < 5; i++) {
                for (let j = 0; j < 5; j++) {
                    const chave = `${i}-${j}`;
                    if (!celulasReveladas.has(chave)) {
                        const celula = document.getElementById(`celula-${i}-${j}`);
                        celula.classList.add('revelada');
                        const valor = matrizComBombas[i][j];
                        if (valor === -1) {
                            celula.innerHTML = '💣';
                            celula.classList.add('bomba');
                        } else {
                            celula.innerHTML = '✓';
                            celula.classList.add('segura');
                        }
                    }
                }
            }
        }

        function mostrarFim(titulo, mensagem) {
            // Mostrar tabela
            document.getElementById('matrixSection').style.display = 'block';
            
            // Mostrar modal
            document.getElementById('modalTitulo').textContent = titulo;
            document.getElementById('modalMensagem').textContent = mensagem;
            document.getElementById('modal').style.display = 'flex';
        }

        function ocultarModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function novoJogo() {
            location.reload();
        }

        // Event listeners
        document.getElementById('novoJogo').addEventListener('click', novoJogo);
        document.getElementById('fecharModal').addEventListener('click', function() {
            ocultarModal();
            novoJogo();
        });

        // Inicializar
        desenharGrafo();
        inicializarCelulas();
    </script>
</body>

</html>