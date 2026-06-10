  // Função para obter o ID do animal da URL
  function getAnimalIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
  }

  // Função para carregar dados do animal
  function loadAnimal() {
    try {
      const animalId = getAnimalIdFromUrl();
      if (!animalId) {
        window.location.href = 'index.html';
        return;
      }

      const storedPets = localStorage.getItem("matchedAnimals");
      pets = JSON.parse(storedPets);
      // Encontrar o animal
      const animal = pets.find(a => a.id === animalId);
      if (!animal) {
        window.location.href = 'index.html';
        return;
      }

      // Renderizar os dados
      renderAnimal(animal);
    } catch (error) {
      console.error('Erro ao carregar animal:', error);
      window.location.href = 'index.html';
    }
  }

  function renderAnimal(animal) {
    // Foto principal
    document.getElementById('fotoMain').src = animal.images[0] || '';
    document.title = `${animal.name} - PetMatch`;

    // Nome e raça
    document.getElementById('nomePet').textContent = animal.name;
    document.getElementById('racaPet').textContent = `${animal.species} • ${animal.breed}`;

    // Badge de match
    const badgeEl = document.getElementById('matchBadge');
    if (animal.match >= 85) {
      badgeEl.className = 'match-badge-detalhe super';
      badgeEl.textContent = '⭐ SUPER MATCH';
    } else if (animal.match >= 70) {
      badgeEl.className = 'match-badge-detalhe bom';
      badgeEl.textContent = `✓ ${animal.match}%`;
    } else {
      badgeEl.className = 'match-badge-detalhe padrao';
      badgeEl.textContent = `${animal.match}%`;
    }

    // Info básica
    const infoBasicaHtml = `
      <div class="info-item">
        <div class="info-label">Idade</div>
        <div class="info-valor">${animal.age}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Sexo</div>
        <div class="info-valor">${animal.sex}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Porte</div>
        <div class="info-valor">${animal.size}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Categoria</div>
        <div class="info-valor">${animal.category}</div>
      </div>
    `;
    document.getElementById('infoBasica').innerHTML = infoBasicaHtml;

    // Comportamentos
    const comportamentosHtml = animal.behaviors
      .map(b => `<div class="tag-detalhe">${b}</div>`)
      .join('');
    document.getElementById('comportamentos').innerHTML = comportamentosHtml || '<div class="tag-detalhe">Dados não disponíveis</div>';

    // Ambientes
    const ambientesHtml = animal.environment
      .map(a => `<div class="tag-detalhe orange">${a}</div>`)
      .join('');
    document.getElementById('ambientes').innerHTML = ambientesHtml || '<div class="tag-detalhe orange">Dados não disponíveis</div>';

    // Compatibilidades
    const compatHtml = animal.compatibilities
      .map(c => `<div class="tag-detalhe yellow">${c}</div>`)
      .join('');
    document.getElementById('compatibilidades').innerHTML = compatHtml || '<div class="tag-detalhe yellow">Dados não disponíveis</div>';

    // Status de saúde
    const statusSaudeHtml = animal.health
      .map(h => `<div class="saude-item">${h}</div>`)
      .join('');
    document.getElementById('statusSaude').innerHTML = statusSaudeHtml || '<div class="saude-item">Sem informações de saúde</div>';

    // Descrição
    document.getElementById('descricaoPet').textContent = animal.description || 'Sem descrição disponível';

    // Contato
    const contatoHtml = `
      <div class="contato-item">
        <div class="contato-label">Nome do responsável</div>
        <div class="contato-valor">${animal.contact?.name || 'Não informado'}</div>
      </div>
      <div class="contato-item">
        <div class="contato-label">Telefone</div>
        <div class="contato-valor">
          ${animal.contact?.phone ? `<a href="tel:${animal.contact.phone}">${animal.contact.phone}</a>` : 'Não informado'}
        </div>
      </div>
      <div class="contato-item">
        <div class="contato-label">Email</div>
        <div class="contato-valor">
          ${animal.contact?.email ? `<a href="mailto:${animal.contact.email}">${animal.contact.email}</a>` : 'Não informado'}
        </div>
      </div>
    `;
    document.getElementById('contatoInfo').innerHTML = contatoHtml;

    // Galeria de miniaturas
    const miniHtml = animal.images
      .map((img, idx) => `
        <div class="miniatura ${idx === 0 ? 'ativa' : ''}" onclick="mudarFoto('${img}', this)">
          <img src="${img}" alt="Foto ${idx + 1}">
        </div>
      `)
      .join('');
    document.getElementById('galeriaMini').innerHTML = miniHtml;

    // Botões
    setupBotoes(animal);
  }

  function mudarFoto(src, element) {
    document.getElementById('fotoMain').src = src;
    document.querySelectorAll('.miniatura').forEach(m => m.classList.remove('ativa'));
    element.classList.add('ativa');
  }

  function setupBotoes(animal) {
    // Favoritar
    const btnFav = document.getElementById('btnFav');
    const isFav = localStorage.getItem(`fav-${animal.id}`);
    if (isFav) {
      btnFav.classList.add('favoritado');
      btnFav.textContent = '❤️ Favoritado';
    }
    btnFav.addEventListener('click', () => {
      const isFav = localStorage.getItem(`fav-${animal.id}`);
      if (isFav) {
        localStorage.removeItem(`fav-${animal.id}`);
        btnFav.classList.remove('favoritado');
        btnFav.textContent = '🤍 Favoritar';
      } else {
        localStorage.setItem(`fav-${animal.id}`, 'true');
        btnFav.classList.add('favoritado');
        btnFav.textContent = '❤️ Favoritado';
      }
    });

    // Enviar mensagem
    const btnMsg = document.getElementById('btnMsg');
    btnMsg.addEventListener('click', () => {
      const phone = animal.contact?.phone;
      if (phone) {
        const msg = `Olá! Tenho interesse no ${animal.name}. Gostaria de saber mais informações.`;
        window.open(`https://wa.me/${phone.replace(/\D/g, '')}?text=${encodeURIComponent(msg)}`, '_blank');
      } else {
        alert('Número de telefone não disponível');
      }
    });

    // Compartilhar
    const btnCompartilhar = document.getElementById('btnCompartilhar');
    btnCompartilhar.addEventListener('click', () => {
      const url = window.location.href;
      const texto = `Conheça o ${animal.name}! Um adorável ${animal.species} à espera de um novo lar. Acesse: ${url}`;
      
      if (navigator.share) {
        navigator.share({
          title: `${animal.name} - PetMatch`,
          text: texto,
          url: url
        });
      } else {
        // Fallback: copiar para clipboard
        navigator.clipboard.writeText(texto).then(() => {
          alert('Link copiado para a área de transferência!');
        });
      }
    });
  }

  // Carregar o animal ao abrir a página
  loadAnimal();
