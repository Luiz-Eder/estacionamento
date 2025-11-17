<?php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\UseCase\RegisterEntry;
use App\Application\UseCase\RegisterExit;
use App\Application\UseCase\GetDashboard;
use App\Infra\Repository\SqliteTicketRepository;

$repo = new SqliteTicketRepository();
$dashboardService = new GetDashboard($repo);

// Lógica de POST (Ações)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'entry') {
            $plate = strtoupper($_POST['plate']);
            $type = $_POST['type'];
            (new RegisterEntry($repo))->execute($plate, $type);
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => "Entrada registrada para $plate!"];
        } 
        elseif ($action === 'exit') {
            $plate = $_POST['plate'];
            $receipt = (new RegisterExit($repo))->execute($plate);
            $total = number_format($receipt['total'], 2, ',', '.');
            $msg = "Saída Confirmada!<br>Placa: {$receipt['plate']}<br>Total: R$ {$total}";
            $_SESSION['flash_message'] = ['type' => 'info', 'text' => $msg];
        }
        elseif ($action === 'reset') {
            $repo->clear();
            $_SESSION['flash_message'] = ['type' => 'warning', 'text' => "Sistema zerado com sucesso!"];
        }
    } catch (Exception $e) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => $e->getMessage()];
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
$data = $dashboardService->execute();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estacionamento SOLID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="container mx-auto p-6 max-w-5xl">
        
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-indigo-600">🅿️ Smart Parking</h1>
                <p class="text-gray-500">Sistema de Controle Inteligente</p>
            </div>
            <form method="POST" action="" onsubmit="return confirm('Apagar tudo?');">
                <input type="hidden" name="action" value="reset">
                <button class="bg-gray-200 hover:bg-red-600 hover:text-white text-gray-600 font-bold py-2 px-4 rounded text-sm transition">
                    🗑️ Zerar
                </button>
            </form>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm uppercase">No Pátio</h3>
                <p class="text-3xl font-bold"><?= count($data['active_vehicles']) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm uppercase">Faturamento</h3>
                <p class="text-3xl font-bold text-green-600">R$ <?= number_format($data['total_revenue'], 2, ',', '.') ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm uppercase">Total Atendimentos</h3>
                <p class="text-3xl font-bold"><?= $data['total_vehicles'] ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow h-fit">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Registrar Entrada</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="entry">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Placa</label>
                        <input type="text" name="plate" required placeholder="ABC-1234" maxlength="8" class="shadow border rounded w-full py-2 px-3 uppercase">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipo</label>
                        <select name="type" class="shadow border rounded w-full py-2 px-3 bg-white">
                            <option value="carro">Carro (R$ 5/h)</option>
                            <option value="moto">Moto (R$ 3/h)</option>
                            <option value="caminhao">Caminhão (R$ 10/h)</option>
                        </select>
                    </div>
                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">Confirmar</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Veículos Estacionados</h2>
                <?php if (empty($data['active_vehicles'])): ?>
                    <p class="text-gray-500 text-center py-4">Pátio vazio.</p>
                <?php else: ?>
                    <table class="min-w-full text-left text-sm">
                        <thead class="uppercase border-b bg-gray-50">
                            <tr><th class="px-6 py-4">Placa</th><th class="px-6 py-4">Tipo</th><th class="px-6 py-4">Entrada</th><th class="px-6 py-4">Ação</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['active_vehicles'] as $v): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold"><?= $v['plate'] ?></td>
                                    <td class="px-6 py-4"><span class="bg-gray-200 px-2 py-1 rounded text-xs"><?= $v['type'] ?></span></td>
                                    <td class="px-6 py-4"><?= $v['entry'] ?></td>
                                    <td class="px-6 py-4">
                                        <form method="POST" onsubmit="return confirm('Saída de <?= $v['plate'] ?>?');">
                                            <input type="hidden" name="action" value="exit">
                                            <input type="hidden" name="plate" value="<?= $v['plate'] ?>">
                                            <button class="text-red-500 hover:text-red-700 font-bold">Saída</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
    <script>
        Swal.fire({
            icon: '<?= $message['type'] ?>',
            title: '<?= $message['type'] === 'error' ? 'Ops...' : 'Sucesso!' ?>',
            html: '<?= $message['text'] ?>',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    <?php endif; ?>
</body>
</html>