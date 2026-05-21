// --- Estado global ---
let pets = [];

// --- Elementos do DOM ---
const galeria = document.getElementById("galeria");
const resultadosInfo = document.getElementById("resultadosInfo");
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const toast = document.getElementById("toast");

menuToggle.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// --- Toast de notificação ---
function showToast(mensagem) {
  toast.textContent = mensagem;
  toast.classList.add("show");
  setTimeout(() => {
    toast.classList.remove("show");
  }, 2000);
}

// --- Classificação de match ---
function getClassificacao(pct) {
  if (pct >= 90) return { texto: "⭐ Super Match", classe: "super" };
  if (pct >= 70) return { texto: "✓ Bom Match",   classe: "bom"   };
  return           { texto: "Match",             classe: "padrao" };
}

// --- Favoritar ---
function toggleFavorito(animalId, animalName) {
  if (localStorage.getItem(`fav-${animalId}`)) {
    localStorage.removeItem(`fav-${animalId}`);
    showToast(`${animalName} removido dos favoritos`);
  } else {
    localStorage.setItem(`fav-${animalId}`, 'true');
    showToast(`${animalName} adicionado aos favoritos! ❤`);
  }
  aplicarFiltros();
}

// --- Filtros ---
function aplicarFiltros() {
  const tipo = document.getElementById("tipoFiltro").value;
  const tamanho = document.getElementById("tamanhoFiltro").value;
  const ambiente = document.getElementById("ambienteFiltro").value;
  const comportamento = document.getElementById("comportamentoFiltro").value;

  const filtrados = pets.filter(pet => {
    // Filtro de tipo
    let passaTipo = tipo === "todos";
    if (!passaTipo) {
      if (tipo === "cao") passaTipo = pet.category?.toLowerCase() === "cão";
      else if (tipo === "gato") passaTipo = pet.category?.toLowerCase() === "gato";
      else if (tipo === "silvestre") passaTipo = pet.category?.toLowerCase() === "silvestre";
    }

    // Filtro de tamanho
    let passaTamanho = tamanho === "todos";
    if (!passaTamanho) {
      const tamanhoMapeado = { pequeno: "Pequeno", medio: "Médio", grande: "Grande" };
      passaTamanho = pet.size === tamanhoMapeado[tamanho];
    }

    // Filtro de ambiente (pet.environment é um array)
    let passaAmbiente = ambiente === "todos";
    if (!passaAmbiente && pet.environment) {
      if (ambiente === "apartamento") {
        passaAmbiente = pet.environment.some(a => a.toLowerCase().includes("apartamento"));
      } else if (ambiente === "casa") {
        passaAmbiente = pet.environment.some(a => a.toLowerCase().includes("casa"));
      }
    }

    // Filtro de comportamento (pet.behaviors é um array)
    let passaComportamento = comportamento === "todos";
    if (!passaComportamento && pet.behaviors) {
      if (comportamento === "calmo") {
        passaComportamento = pet.behaviors.some(b => b.toLowerCase().includes("calmo") || b.toLowerCase().includes("tranquilo"));
      } else if (comportamento === "ativo") {
        passaComportamento = pet.behaviors.some(b => b.toLowerCase().includes("ativo") || b.toLowerCase().includes("alegre") || b.toLowerCase().includes("brincalhão"));
      }
    }

    return passaTipo && passaTamanho && passaAmbiente && passaComportamento;
  });

  renderPets(filtrados);
}

// Listeners dos selects
document.querySelectorAll("select").forEach(select => {  select.addEventListener("change", aplicarFiltros);});

// Botão limpar filtros
document.getElementById("btnLimpar").addEventListener("click", () => {
  document.querySelectorAll("select").forEach(s => s.value = "todos");
  aplicarFiltros();
  showToast("Filtros limpos!");
});


// ======================================
// CARREGA MATCHES SALVOS
// ======================================

function carregarPets(){

  try {
    // lê localStorage
    const storedPets = localStorage.getItem("matchedAnimals");
    // valida
    if(!storedPets){
      galeria.innerHTML = ` <div class="estado-vazio">  <span class="vazio-icon">🐾</span>  <p>Nenhum match encontrado.</p>
          <small> Preencha seu perfil primeiro.  </small>  </div> `;
      return;
    }
    pets = JSON.parse(storedPets);
    if(!Array.isArray(pets)){ throw new Error("Formato inválido de matches."); }
    renderPets(pets);
  } catch(error){ console.error( "Erro ao carregar matches:",  error );
    galeria.innerHTML = ` <div class="estado-vazio">  <span class="vazio-icon">⚠️</span>  <p>Erro ao carregar matches.</p>  </div> `;
  }
}

function renderPets(lista = []) {

  galeria.innerHTML = "";

  // vazio
  if (!lista || lista.length === 0) {

    galeria.innerHTML = `
      <div class="estado-vazio">
        <span class="vazio-icon">🐾</span>
        <p>Nenhum pet encontrado.</p>
        <small>
          Tente alterar os filtros.
        </small>
      </div>
    `;

    resultadosInfo.textContent = "";

    return;
  }

  // contador
  resultadosInfo.textContent = `${lista.length} ${
      lista.length > 1  ? "pets encontrados"  : "pet encontrado" }`;

  // ordena por match
  lista.sort((a, b) => {

    const scoreA =  Number(a.match) || 0;
    const scoreB =  Number(b.match) || 0;

    return scoreB - scoreA;
  });

  // renderiza cards
  lista.forEach(animal => { galeria.appendChild( renderCard(animal)); });

  // eventos favoritos
  galeria.querySelectorAll(".btn-fav, .btn-fav-card").forEach(btn => { btn.addEventListener( "click",  (e) => { e.stopPropagation();  } ); });
}

// ======================================
// INICIALIZA
// ======================================

carregarPets();

// by cristian
function renderCard(animal) {
  const matchScore = animal.match;
  let matchClass = 'padrao';
  let matchText = `${matchScore}%`;

  if (matchScore >= 85) {
    matchClass = 'super';
    matchText = '⭐ SUPER MATCH';
  } else if (matchScore >= 70) {
    matchClass = 'bom';
    matchText = `✓ ${matchScore}%`;
  }

  const card = document.createElement('div');
  card.className = 'card';
  card.innerHTML = `
    <div class="card-img-wrap">
      <img loading="lazy" src="${animal.images[0]}" alt="${animal.name}" />
      <div class="match-badge ${matchClass}">${matchText}</div>
      <button class="btn-fav" id="fav-${animal.id}" aria-label="Adicionar aos favoritos">🤍</button>
    </div>
    <div class="card-content">
      <div class="card-header">
        <span class="nome">${animal.name}</span>
        <div class="tags">
          <span class="tag">${animal.species}</span>
          <span class="tag">${animal.size}</span>
        </div>
      </div>
      <p class="caracteristicas">${animal.behaviors.join(', ')}</p>
      <div class="botoes">
        <button class="btn-detalhes" data-animal-id="${animal.id}">Ver Detalhes</button>
        <button class="btn-fav-card" data-animal-id="${animal.id}">❤️</button>
      </div>
    </div>
  `;

  // ADICIONAR EVENT LISTENER PARA NAVEGAÇÃO
  const btnDetalhes = card.querySelector('.btn-detalhes');
  btnDetalhes.addEventListener('click', () => {
    window.location.href = `detalhe/index.html?id=${animal.id}`;
  });

  // Botões de favorito
  const btnFav = card.querySelector(`#fav-${animal.id}`);
  const btnFavCard = card.querySelector('.btn-fav-card');
  
  // Verificar se já é favorito
  if (localStorage.getItem(`fav-${animal.id}`)) {
    btnFav.textContent = '❤️';
    btnFavCard.textContent = '❤️';
    btnFavCard.classList.add('favoritado');
  }

  // Toggle favorito
  const toggleFav = () => {
    toggleFavorito(animal.id, animal.name);
    // Atualizar visual dos botões
    if (localStorage.getItem(`fav-${animal.id}`)) {
      btnFav.textContent = '❤️';
      btnFavCard.textContent = '❤️';
      btnFavCard.classList.add('favoritado');
    } else {
      btnFav.textContent = '🤍';
      btnFavCard.textContent = '❤️';
    }
  };

  btnFav.addEventListener('click', toggleFav);
  btnFavCard.addEventListener('click', toggleFav);

  return card;
}

// ALTERNATIVA: Se você quer clicar no card inteiro
function makeCardClickable(card, animalId) {
  card.style.cursor = 'pointer';
  
  card.addEventListener('click', (e) => {
    // Não navegar se clicou nos botões
    if (e.target.closest('.btn-fav, .botoes')) {
      return;
    }
    window.location.href = `detalhe.html?id=${animalId}`;
  });
}