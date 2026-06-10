# Documentação - Página de Detalhes do Animal

## Visão Geral
A página de detalhes exibe informações completas sobre um animal específico, carregando dados dinamicamente do arquivo `animals.json`.

## Como Usar

### URL Pattern
A página aceita um parâmetro de query string `id`:
```
detalhe.html?id=animal-001
animal-detalhe.html?id=animal-001
```

### Exemplo de Link
```html
<a href="/detalhe/index.html?id=animal-001">Ver Detalhes</a>
```

## Dados Exibidos

### Informações Básicas
- **Nome do pet**: Nome do animal
- **Foto principal**: Primeira imagem da galeria
- **Raça**: Species e Breed
- **Idade**: Age
- **Sexo**: Sex
- **Porte**: Size
- **Categoria**: Category (Cão, Gato, etc)

### Características Comportamentais
- Exibe array `behaviors` como tags
- Exemplo: "Ativo", "Carinhoso", "Brincalhão"

### Ambiente Ideal
- Exibe array `environment` como tags laranja
- Exemplo: "Apartamento", "Casa com Quintal", "Sítio"

### Compatibilidades
- Exibe array `compatibilities` como tags amarelas
- Exemplo: "Bom com Crianças", "Pode ficar sozinho"

### Status de Saúde
- Exibe array `health` com checkmarks verdes
- Exemplo: "Vacinado", "Castrado", "Vermifugado"

### Descrição
- Campo `description`: História e características do pet
- Também utiliza `story` se disponível

### Informações do Responsável
- `contact.name`: Nome do responsável
- `contact.phone`: Telefone (com link para WhatsApp)
- `contact.email`: Email (com link mailto)

### Match Score
- **>= 85**: "⭐ SUPER MATCH" (fundo verde)
- **70-84**: "✓ XX%" (fundo amarelo)
- **< 70**: "XX%" (fundo branco)

## Ações Disponíveis

### 1. Enviar Mensagem
- Botão: "💬 Enviar mensagem"
- Abre WhatsApp com mensagem pré-digitada
- Requer `contact.phone` no JSON

### 2. Favoritar
- Botão: "🤍 Favoritar" / "❤️ Favoritado"
- Armazena preferência em `localStorage`
- Chave: `fav-{animal.id}`

### 3. Compartilhar
- Botão: "📤 Compartilhar"
- Usa Web Share API (se disponível)
- Fallback: copia link para clipboard

## Galeria de Fotos
- Primeira imagem como foto principal
- Miniaturas abaixo para navegação
- Click para mudar a foto principal
- Array `images` do JSON

## Integração com index.html

Para integrar com sua página principal, adicione ao botão de detalhes:

```javascript
// Quando clica no card
card.addEventListener('click', () => {
  window.location.href = `detalhe.html?id=${animal.id}`;
});
```

Ou use um link direto:
```html
<a href="detalhe.html?id=${animal.id}" class="btn-detalhes">
  Ver Detalhes
</a>
```

## Estrutura JSON Esperada

```json
{
  "id": "animal-001",
  "name": "Luna",
  "category": "Cão",
  "species": "Poodle",
  "breed": "Poodle",
  "age": "4 anos",
  "sex": "Fêmea",
  "size": "Médio",
  "matchScore": 91,
  "behaviors": ["Independente", "Alegre"],
  "environment": ["Apartamento"],
  "compatibilities": ["Adora receber visitas"],
  "health": ["Vermifugado", "Vacinado"],
  "description": "Luna é um animal...",
  "story": "Encontrado em...",
  "images": ["url1.jpg", "url2.jpg"],
  "contact": {
    "name": "João Silva",
    "phone": "+55 11 98765-4321",
    "email": "joao@email.com"
  }
}
```

## Recursos de Design

### Cores
- Verde: `#2d8a60`
- Laranja: `#e8723a`
- Amarelo: `#f5c842`
- Cinza BG: `#f7f6f2`

### Responsividade
- Desktop: Layout 2 colunas (galeria + info)
- Tablet: Layout 1 coluna
- Mobile: Layout flexível com ajustes

### Typography
- Font: Nunito (400, 600, 700, 800)
- Título: 32px (Desktop), 24px (Tablet), 20px (Mobile)

## Erros e Fallbacks

- Se não houver `id` na URL: redireciona para `index.html`
- Se animal não for encontrado: redireciona para `index.html`
- Se não houver dados de contato: exibe "Não informado"
- Se não houver imagens: campo vazio (mostra apenas a cor de fundo)

## Compatibilidade

- Todos os browsers modernos
- JavaScript ES6+
- LocalStorage para favoritos
- Fetch API para carregar JSON
