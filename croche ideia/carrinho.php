<?php
session_start();

// 1. Simulação de Banco de Dados de produtos
$produtos = [
    1 => ['nome' => 'Vela Aromática', 'preco' => 45.90, 'img' => 'https://via.placeholder.com/150'],
    2 => ['nome' => 'Difusor de Ambiente', 'preco' => 89.00, 'img' => 'https://via.placeholder.com/150'],
    3 => ['nome' => 'Sabonete Artesanal', 'preco' => 22.50, 'img' => 'https://via.placeholder.com/150']
];

// 2. Lógica para adicionar
if (isset($_GET['adicionar'])) {
    $id = (int) $_GET['adicionar'];
    if (isset($produtos[$id])) {
        $_SESSION['carrinho'][$id] = ($_SESSION['carrinho'][$id] ?? 0) + 1;
    }
    header("Location: carrinho.php");
    exit;
}

// 3. Lógica para limpar
if (isset($_GET['limpar'])) {
    unset($_SESSION['carrinho']);
    header("Location: carrinho.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - Estilo Pastel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" />
    <style>
        :root {
            --pastel-pink: #e0c4a0;
            --pastel-purple: #c0866f;
            --pastel-lavender: #f5e9d4;
            --pastel-cream: #fefbf0;
            --pastel-peach: #35442f;
            --pastel-mint: #918e5f;
            --pastel-dark: #212809;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Montserrat", sans-serif;
            font-weight: 300;
            background-color: var(--pastel-cream);
            color: var(--pastel-dark);
            line-height: 1.6;
        }

        h1,
        h2,
        h3 {
            font-family: "Lora", serif;
            font-weight: 600;
        }

        .btn-pastel-pink {
            background-color: var(--pastel-pink);
            border-color: var(--pastel-pink);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-pastel-pink:hover {
            background-color: #c9b08f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(224, 196, 160, 0.3);
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(192, 134, 111, 0.1) !important;
        }

        .table {
            --bs-table-bg: transparent;
            color: var(--pastel-dark);
            font-family: "Montserrat", sans-serif;
        }

        .bg-pastel-lavender {
            background-color: var(--pastel-lavender) !important;
        }

        .total-box {
            background-color: var(--pastel-lavender);
            border-radius: 12px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <nav class="navbar bg-pastel-lavender mb-5 py-3 shadow-sm">
        <div class="container justify-content-center">
            <h2 class="m-0" style="color: var(--pastel-purple);">Minha Loja</h2>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4">

            <div class="col-lg-8">
                <h3 class="mb-4">Produtos Disponíveis</h3>
                <div class="row g-3">
                    <?php foreach ($produtos as $id => $item): ?>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex align-items-center">
                                    <img src="<?php echo $item['img']; ?>" class="rounded-3 me-3" width="70" alt="produto">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1"><?php echo $item['nome']; ?></h5>
                                        <p class="text-muted small mb-2">R$
                                            <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                        <a href="?adicionar=<?php echo $id; ?>" class="btn btn-pastel-pink btn-sm w-100">
                                            Adicionar ao Carrinho
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="mb-4">Seu Carrinho</h3>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead class="border-bottom">
                                    <tr class="small text-uppercase">
                                        <th>Item</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    if (!empty($_SESSION['carrinho'])):
                                        foreach ($_SESSION['carrinho'] as $id => $qtd):
                                            $subtotal = $produtos[$id]['preco'] * $qtd;
                                            $total += $subtotal;
                                            ?>
                                            <tr>
                                                <td class="py-3">
                                                    <span class="d-block fw-bold"><?php echo $produtos[$id]['nome']; ?></span>
                                                    <span class="small text-muted">R$
                                                        <?php echo number_format($produtos[$id]['preco'], 2, ',', '.'); ?></span>
                                                </td>
                                                <td class="text-center"><?php echo $qtd; ?></td>
                                                <td class="text-end fw-bold">R$
                                                    <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                O carrinho está vazio.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total > 0): ?>
                            <div class="total-box mt-3">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total</span>
                                    <h4 class="m-0" style="color: var(--pastel-purple);">
                                        R$ <?php echo number_format($total, 2, ',', '.'); ?>
                                    </h4>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-pastel-pink py-2" onclick="finalizar()">Finalizar Compra</button>
                                    <a href="?limpar=1" class="btn btn-link btn-sm text-muted text-decoration-none">Limpar
                                        Carrinho</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center py-5 mt-5">
        <p class="text-muted small">&copy; 2024 Minha Loja Pastel - Feito com PHP</p>
    </footer>

    <script>
        function finalizar() {
            const total = "<?php echo number_format($total, 2, ',', '.'); ?>";
            alert("Pedido confirmado! Total: R$ " + total + "\nObrigado pela preferência.");
        }
    </script>
</body>

</html>