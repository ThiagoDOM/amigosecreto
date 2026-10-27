/**
 * GiftMatch Themes Configuration & Gift Discovery Helpers
 */

export const THEMES = {
    traditional: {
        id: 'traditional',
        label: 'Tradicional',
        title: 'Amigo Secreto Tradicional',
        emoji: '🎁',
        tagline: 'Qualquer presente dentro do valor combinado',
        description: 'Tema livre! Os participantes indicam o que mais gostariam de ganhar em suas listas de desejos.',
        badgeClass: 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        cardBorderClass: 'border-rose-500/30 hover:border-rose-500/60',
        giftIdeas: [
            { title: 'Garrafa Térmica Inox', category: 'Utilidades', price: 'R$ 40 - R$ 70' },
            { title: 'Fone de Ouvido Bluetooth', category: 'Tecnologia', price: 'R$ 60 - R$ 120' },
            { title: 'Kit Cuidados Corporais / Perfumaria', category: 'Beleza', price: 'R$ 50 - R$ 100' },
            { title: 'Camiseta Básica Premium', category: 'Moda', price: 'R$ 45 - R$ 80' },
            { title: 'Luminária Decorativa de Mesa', category: 'Casa', price: 'R$ 35 - R$ 65' },
            { title: 'Jogo de Tabuleiro / Cartas Divertido', category: 'Lazer', price: 'R$ 40 - R$ 80' },
        ],
    },
    chocolate: {
        id: 'chocolate',
        label: 'Amigo Chocolate',
        title: 'Amigo Secreto Chocolate & Doces',
        emoji: '🍫',
        tagline: 'Caixas de bombom, barras artesanais, trufas e doces finos',
        description: 'Perfeito para Páscoa ou confraternizações doces. O presente deve ser chocolate ou doces gourmet!',
        badgeClass: 'bg-amber-950/80 text-amber-300 border-amber-700/50',
        cardBorderClass: 'border-amber-700/40 hover:border-amber-600/70',
        giftIdeas: [
            { title: 'Caixa de Bombons Finos Ferrero Rocher / Lindt', category: 'Chocolates Finos', price: 'R$ 35 - R$ 65' },
            { title: 'Cesta de Chocolates & Trufas Artesanais', category: 'Cestas', price: 'R$ 50 - R$ 90' },
            { title: 'Kit Barras de Chocolate 70% Cacau Especial', category: 'Gourmet', price: 'R$ 40 - R$ 75' },
            { title: 'Pote de Nutella Grande + Panetone Trufado', category: 'Doces', price: 'R$ 45 - R$ 80' },
            { title: 'Caixa Degustação Kopenhagen ou Cacau Show', category: 'Presentes Finos', price: 'R$ 50 - R$ 110' },
            { title: 'Fondue de Chocolate Elétrico / Aparelho', category: 'Acessórios', price: 'R$ 60 - R$ 110' },
        ],
    },
    mug: {
        id: 'mug',
        label: 'Amigo Caneca',
        title: 'Amigo Secreto Caneca Criativa',
        emoji: '☕',
        tagline: 'Canecas criativas, térmicas, personalizadas e copos especiais',
        description: 'O presente deve ser uma caneca divertida, copo térmico ou xícara estilosa para café ou chá.',
        badgeClass: 'bg-orange-950/80 text-orange-300 border-orange-700/50',
        cardBorderClass: 'border-orange-700/40 hover:border-orange-600/70',
        giftIdeas: [
            { title: 'Copo Térmico Inox com Tampa (Tipo Stanley)', category: 'Térmicos', price: 'R$ 45 - R$ 85' },
            { title: 'Caneca Gamer / Série / Filme Criativa', category: 'Geek', price: 'R$ 35 - R$ 60' },
            { title: 'Caneca Térmica com Aquecedor USB', category: 'Tecnologia', price: 'R$ 50 - R$ 90' },
            { title: 'Caneca Esmaltada Vintage + Café Gourmet', category: 'Kit Café', price: 'R$ 40 - R$ 75' },
            { title: 'Caneca Mágica Termossensível que muda de cor', category: 'Criativas', price: 'R$ 35 - R$ 55' },
            { title: 'Xícara de Porcelana Fina com Pires Trabalhado', category: 'Sofisticado', price: 'R$ 45 - R$ 80' },
        ],
    },
    book: {
        id: 'book',
        label: 'Amigo Livro',
        title: 'Amigo Secreto Livro & Literatura',
        emoji: '📚',
        tagline: 'Bestsellers, romances, desenvolvimento, HQs e mangás',
        description: 'O presente deve ser um livro físico ou leitura inesquecível escolhida para a estante do amigo!',
        badgeClass: 'bg-blue-950/80 text-blue-300 border-blue-700/50',
        cardBorderClass: 'border-blue-700/40 hover:border-blue-600/70',
        giftIdeas: [
            { title: 'Bestseller de Ficção / Romance do Ano', category: 'Literatura', price: 'R$ 40 - R$ 70' },
            { title: 'Livro de Desenvolvimento Pessoal / Hábitos', category: 'Não-Ficção', price: 'R$ 38 - R$ 65' },
            { title: 'Box Colecionador / Trilogia Especial', category: 'Coleção', price: 'R$ 60 - R$ 130' },
            { title: 'Quadrinho / Graphic Novel / Mangá Edição de Luxo', category: 'Geek', price: 'R$ 45 - R$ 90' },
            { title: 'Luminária Clip-on para Leitura Noturna', category: 'Acessório', price: 'R$ 25 - R$ 45' },
            { title: 'Kit Marcadores Magnéticos + Capa de Livro', category: 'Acessório', price: 'R$ 30 - R$ 50' },
        ],
    },
    havaianas: {
        id: 'havaianas',
        label: 'Amigo Chinelo',
        title: 'Amigo Secreto Chinelo & Sandália',
        emoji: '🩴',
        tagline: 'Chinelos Havaianas, Rider, sandálias rasteiras e slides',
        description: 'Perfeito para o verão e festas de fim de ano! Consulte a numeração do calçado do sorteado.',
        badgeClass: 'bg-teal-950/80 text-teal-300 border-teal-700/50',
        cardBorderClass: 'border-teal-700/40 hover:border-teal-600/70',
        giftIdeas: [
            { title: 'Havaianas Slim / Top Estampada de Coleção', category: 'Casual', price: 'R$ 35 - R$ 65' },
            { title: 'Chinelo Slide Anatômico Conforto', category: 'Slide', price: 'R$ 50 - R$ 90' },
            { title: 'Sandália Rasteira Elegante Feminina', category: 'Feminino', price: 'R$ 45 - R$ 85' },
            { title: 'Havaianas com Detalhe Metálico / Glitter', category: 'Especial', price: 'R$ 45 - R$ 80' },
            { title: 'Chinelo Rider / Kenner Macio Masculino', category: 'Masculino', price: 'R$ 55 - R$ 95' },
            { title: 'Kit Chinelo + Sacola de Praia / Pano de Piscina', category: 'Kit Verão', price: 'R$ 50 - R$ 90' },
        ],
    },
    funny: {
        id: 'funny',
        label: 'Amigo da Onça / Troll',
        title: 'Amigo da Onça (Presentes Divertidos & Pegadinhas)',
        emoji: '🤪',
        tagline: 'Presentes cômicos, zoeiras leves e itens inusitados',
        description: 'Vale dar presentes engraçados, meias com estampas bizarras, almofadas meme ou itens de pegadinha!',
        badgeClass: 'bg-purple-950/80 text-purple-300 border-purple-700/50',
        cardBorderClass: 'border-purple-700/40 hover:border-purple-600/70',
        giftIdeas: [
            { title: 'Meias com Estampas Engraçadas ou Meme', category: 'Vestuário Cômico', price: 'R$ 25 - R$ 45' },
            { title: 'Almofada de Meme / Emoticon Gigante', category: 'Decoração Zueira', price: 'R$ 35 - R$ 60' },
            { title: 'Porta-Retrato com Foto Cômica dos Amigos', category: 'Personalizado', price: 'R$ 20 - R$ 40' },
            { title: 'Troféu "Melhor Amigo da Onça" Personalizado', category: 'Zoeira', price: 'R$ 30 - R$ 55' },
            { title: 'Jogo "Eu Nunca" ou Verdade ou Desafio', category: 'Jogos de Festa', price: 'R$ 35 - R$ 65' },
            { title: 'Camiseta com Frase Irônica ou Meme', category: 'Vestuário', price: 'R$ 40 - R$ 70' },
        ],
    },
    custom: {
        id: 'custom',
        label: 'Personalizado',
        title: 'Amigo Secreto Temático Personalizado',
        emoji: '🎯',
        tagline: 'Tema livre acordado entre todos os participantes',
        description: 'Tema combinado diretamente pelo grupo (ex: cosméticos, plantas, brechó, artesanato, etc.).',
        badgeClass: 'bg-pink-950/80 text-pink-300 border-pink-700/50',
        cardBorderClass: 'border-pink-700/40 hover:border-pink-600/70',
        giftIdeas: [
            { title: 'Kit Presente com Sacola e Cartão Dedicado', category: 'Geral', price: 'R$ 35 - R$ 75' },
            { title: 'Planta Suculenta em Vaso de Cerâmica Criativo', category: 'Decoração', price: 'R$ 30 - R$ 55' },
            { title: 'Vela Aromática Artesanal com Óleo Essencial', category: 'Bem-Estar', price: 'R$ 35 - R$ 65' },
            { title: 'Caderno / Planner Artesanal Capa Dura', category: 'Papelaria', price: 'R$ 35 - R$ 60' },
            { title: 'Porta-Jóias / Organizador Compacto', category: 'Organização', price: 'R$ 40 - R$ 75' },
            { title: 'Kit Chás Especiais / Sachês Importados', category: 'Gourmet', price: 'R$ 35 - R$ 70' },
        ],
    },
};

/**
 * Returns theme config object, falling back to traditional.
 */
export function getTheme(themeId) {
    if (themeId && THEMES[themeId]) {
        return THEMES[themeId];
    }
    return THEMES.traditional;
}

/**
 * Generates direct search queries for online partner stores.
 */
export function buildStoreSearchUrls(query) {
    const cleanQuery = (query || '').trim();
    const encoded = encodeURIComponent(cleanQuery);

    const shopeeBase = (import.meta.env.VITE_AFFILIATE_SHOPEE_URL || '').trim();
    const mlBase = (import.meta.env.VITE_AFFILIATE_MERCADOLIVRE_URL || '').trim();

    return {
        shopee: shopeeBase && !cleanQuery ? shopeeBase : `https://shopee.com.br/search?keyword=${encoded}`,
        mercadolivre: mlBase && !cleanQuery ? mlBase : `https://lista.mercadolivre.com.br/${encoded}`,
        amazon: `https://www.amazon.com.br/s?k=${encoded}`,
    };
}
