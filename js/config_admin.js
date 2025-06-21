document.addEventListener('DOMContentLoaded', function() {
    // Aplicar configurações ao carregar
    aplicarConfiguracoesVisuais();
    
    // Configurar eventos de mudança
    document.getElementById('modo-escuro').addEventListener('change', aplicarConfiguracoesVisuais);
    document.getElementById('tema-cor').addEventListener('change', aplicarConfiguracoesVisuais);
    document.getElementById('fonte').addEventListener('change', aplicarConfiguracoesVisuais);
    document.getElementById('tamanho-fonte').addEventListener('change', aplicarConfiguracoesVisuais);
    
    
    
});

// Função principal para aplicar configurações
function aplicarConfiguracoesVisuais() {
    // Modo dark/light
    if (document.getElementById('modo-escuro').checked) {
        document.documentElement.classList.add('dark-mode');
    } else {
        document.documentElement.classList.remove('dark-mode');
    }
    
    // Cor do tema
    const corTema = document.getElementById('tema-cor').value;
    document.documentElement.style.setProperty('--primary-color', corTema);
    
    // Fonte do sistema
    const fonte = document.getElementById('fonte').value;
    document.documentElement.style.setProperty('--font-family', fonte);
    
    // Tamanho da fonte
    const tamanhoFonte = document.getElementById('tamanho-fonte').value;
    document.documentElement.style.setProperty('--font-size', tamanhoFonte);
    
    // Aplicar a fonte para elementos específicos que não herdam corretamente
    aplicarFonteElementosEspecificos();
}

// Função para corrigir herança de fonte em elementos problemáticos
function aplicarFonteElementosEspecificos() {
    const fonte = document.getElementById('fonte').value;
    const elementos = document.querySelectorAll('input, button, select, textarea');
    
    elementos.forEach(el => {
        el.style.fontFamily = fonte;
    });
}

// Função para salvar no banco de dados
async function salvarAparencia() {
    const dados = {
        modo_escuro: document.getElementById('modo-escuro').checked,
        tema_cor: document.getElementById('tema-cor').value,
        fonte: document.getElementById('fonte').value,
        tamanho_fonte: document.getElementById('tamanho-fonte').value
    };
    
    try {
        const response = await fetch('salvar_config.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                secao: 'aparencia',
                dados: dados
            })
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            alert('Configurações salvas com sucesso!');
        } else {
            alert('Erro ao salvar: ' + (resultado.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Falha na comunicação com o servidor');
    }
}