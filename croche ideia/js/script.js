document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            // Validação do lado do cliente
            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Enviar via AJAX
            const formData = new FormData(form);

            fetch('processar_contato.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.classList.remove('d-none');
                        errorMessage.classList.add('d-none');
                        form.reset();
                        form.classList.remove('was-validated');

                        // Rolar até a mensagem
                        successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        errorMessage.classList.remove('d-none');
                        successMessage.classList.add('d-none');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    errorMessage.classList.remove('d-none');
                    successMessage.classList.add('d-none');
                });
        });
    }

    // Feedback visual dos campos
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.style.borderColor = 'var(--pastel-pink)';
        });

        input.addEventListener('blur', function () {
            if (this.value === '' && !this.hasAttribute('required')) {
                this.style.borderColor = '';
            }
        });
    });
});
// Dados simulados do carrinho
const carrinho = [
    {
        id: 1,
        nome: "Manta de Crochê",
        descricao: "Manta aconchegante em tons naturais",
        preco: 89.90,
        quantidade: 1,
        cor: "Bege"
    },
    {
        id: 2,
        nome: "Almofada Decorativa",
        descricao: "Almofada quadrada em padrão geométrico",
        preco: 45.00,
        quantidade: 2,
        cor: "Marrom e Bege"
    },
    {
        id: 3,
        nome: "Tapete Redondo",
        descricao: "Tapete artesanal em crochê",
        preco: 120.00,
        quantidade: 1,
        cor: "Verde oliva"
    }
];

const frete = 10.00;
let desconto = 0;

// Renderizar carrinho
function renderizarCarrinho() {
    const container = document.getElementById('itens-container');

    if (carrinho.length === 0) {
        container.innerHTML = `
                    <div class="carrinho-vazio">
                        <div class="carrinho-vazio-icon">🧶</div>
                        <h3>Seu carrinho está vazio</h3>
                        <p class="text-muted mb-3">Adicione alguns itens de crochê para começar!</p>
                        <button class="btn-pastel-pink" onclick="voltarProdutos()">Continuar Comprando</button>
                    </div>
                `;
        return;
    }

    container.innerHTML = carrinho.map(item => `
                <div class="item-carrinho">
                    <div class="item-imagem">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="item-info">
                        <div class="item-nome">${item.nome}</div>
                        <div class="item-descricao">${item.descricao}</div>
                        <div class="item-descricao"><strong>Cor:</strong> ${item.cor}</div>
                        <div class="item-preco">R$ ${item.preco.toFixed(2)}</div>
                        <div class="item-quantidade">
                            <label>Quantidade:</label>
                            <input type="number" value="${item.quantidade}" min="1" max="99" onchange="atualizarQuantidade(${item.id}, this.value)">
                        </div>
                        <div class="item-acoes">
                            <button class="btn-remover" onclick="removerItem(${item.id})">Remover</button>
                            <button class="btn-salvar" onclick="salvarParaDepois(${item.id})">Salvar para depois</button>
                        </div>
                    </div>
                </div>
            `).join('');

    atualizarResumo();
}

// Atualizar quantidade
function atualizarQuantidade(id, novaQuantidade) {
    const item = carrinho.find(i => i.id === id);
    if (item) {
        item.quantidade = parseInt(novaQuantidade) || 1;
        atualizarResumo();
    }
}

// Remover item
function removerItem(id) {
    const index = carrinho.findIndex(i => i.id === id);
    if (index > -1) {
        const item = carrinho[index];
        carrinho.splice(index, 1);
        alert(`${item.nome} removido do carrinho`);
        renderizarCarrinho();
    }
}

// Salvar para depois
function salvarParaDepois(id) {
    const item = carrinho.find(i => i.id === id);
    if (item) {
        alert(`${item.nome} salvo para depois!`);
        removerItem(id);
    }
}

// Atualizar resumo
function atualizarResumo() {
    const subtotal = carrinho.reduce((total, item) => total + (item.preco * item.quantidade), 0);
    const descontoAplicado = subtotal * (desconto / 100);
    const total = subtotal - descontoAplicado + frete;

    document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2)}`;
    document.getElementById('frete').textContent = `R$ ${frete.toFixed(2)}`;
    document.getElementById('total').textContent = `R$ ${total.toFixed(2)}`;

    if (desconto > 0) {
        document.getElementById('desconto-linha').style.display = 'flex';
        document.getElementById('valor-desconto').textContent = `-R$ ${descontoAplicado.toFixed(2)}`;
    }
}

// Aplicar desconto
function aplicarDesconto() {
    const codigo = document.getElementById('codigo-desconto').value;

    // Códigos de desconto simulados
    const codigos = {
        'PRIMEIRACOMPRA': 10,
        'DESCONTO20': 20,
        'CROCHE15': 15
    };

    if (codigos[codigo]) {
        desconto = codigos[codigo];
        alert(`Desconto de ${desconto}% aplicado com sucesso!`);
        document.getElementById('codigo-desconto').value = '';
        atualizarResumo();
    } else {
        alert('Código de desconto inválido');
        desconto = 0;
        atualizarResumo();
    }
}

// Voltar aos produtos
function voltarProdutos() {
    alert('Redirecionando para a página de produtos...');
    // window.location.href = 'produtos.html';
}

// Finalizar compra
function finalizarCompra() {
    if (carrinho.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    alert('Redirecionando para pagamento...');
    // window.location.href = 'checkout.html';
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    renderizarCarrinho();
});
