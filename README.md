# 🎮 Jogo do Grafo - Bandeirinhas e Bombas

Um jogo interativo baseado em grafos onde você precisa encontrar todas as células seguras e evitar as bombas!

## 📋 Como Jogar

### Objetivo
Encontre todas as células seguras sem clicar em nenhuma bomba!

### Controles
- **Clique Esquerdo**: Revelar uma célula
- **Clique Direito**: Marcar/desmarcar uma célula com bandeira (para lembrar onde as bombas podem estar)

### Mecânica do Jogo
1. O tabuleiro é uma matriz 5x5 com células ocultas
2. Cada célula contém um número (representando a matriz de adjacência) ou uma bomba (💣)
3. Clique em uma célula para revelá-la:
   - Se for uma **célula segura**: ela mostra o número e você continua jogando
   - Se for uma **bomba**: GAME OVER!
4. Para **vencer**: revele todas as células seguras
5. Para **recomeçar**: clique em "Novo Jogo"

## 🎨 Interface

- **Matriz de Adjacência**: Mostra os dados do grafo à direita
- **Estatísticas**: Acompanhe quantas células você abriu e o status do jogo
- **Botões**:
  - "Novo Jogo": Inicia uma nova partida com uma matriz aleatória
  - "Mostrar Matriz": Alterna a visibilidade da matriz de adjacência

## 🛠️ Instalação

1. Copie os arquivos para o seu servidor web (XAMPP, WAMP, etc.)
2. Acesse `http://localhost/game_grafos/index.php`
3. Comece a jogar!

## 📁 Arquivos

- `index.php` - Lógica PHP e HTML principal
- `style.css` - Estilos e design responsivo
- `script.js` - Lógica JavaScript (integrada no HTML)

## 🎯 Dicas

- Observe a matriz de adjacência para entender a estrutura do grafo
- Use as bandeiras para marcar células suspeitas
- Quanto mais células reveladas com segurança, mais perto da vitória você está!

## 🌟 Características

✨ Design moderno e responsivo
🎨 Cores vibrantes com tema dark
⚡ Animações suaves e feedback visual
📱 Funciona em desktop e mobile
🔄 Novas matrizes a cada jogo

---

**Desenvolvido com ❤️**
