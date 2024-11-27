<?php

function optimizePurchase(array $priceList, int $N): array {
    
    $priceList = array_filter($priceList, function ($item) {
        return $item['count'] >= $item['pack'];
    });// Фильтрация неподходящих строк

    $totalAvailable = array_sum(array_column($priceList, 'count'));
    // Проверка возможности закупки
    if ($totalAvailable < $N) {
        return []; // Решение невозможно
    }

    usort($priceList, function ($a, $b) {
        $unitPriceA = $a['price'] / $a['pack'];
        $unitPriceB = $b['price'] / $b['pack'];
        return $unitPriceA <=> $unitPriceB;
    }); // Сортировка по цене за единицу

    $plan = []; // Результирующий план закупки
    $remaining = $N; // Оставшаяся потребность

    foreach ($priceList as $item) {
        if ($remaining <= 0) {
            break;
        }

        $maxQty = floor($item['count'] / $item['pack']) * $item['pack'];
        // Максимально доступное количество с учетом кратности

        $qty = min($maxQty, floor($remaining / $item['pack']) * $item['pack']);
        // Выбираем, сколько взять

        if ($qty > 0) {
            $plan[] = [$item['id'], $qty];
            $remaining -= $qty;
        }
    }

    if ($remaining > 0) {
        return [];
    }
    // Если не удалось закрыть потребность

    return $plan;
}

/* Пример данных */
$N1 = 76;
$priceList1 = [
    ['id' => 111, 'count' => 42, 'price' => 13, 'pack' => 1],
    ['id' => 222, 'count' => 77, 'price' => 11, 'pack' => 10],
    ['id' => 333, 'count' => 103, 'price' => 10, 'pack' => 50],
    ['id' => 444, 'count' => 65, 'price' => 12, 'pack' => 5],
];

$priceList2 = [
    ['id' => 111, 'count' => 42, 'price' => 9, 'pack' => 1],
    ['id' => 222, 'count' => 77, 'price' => 11, 'pack' => 10],
    ['id' => 333, 'count' => 103, 'price' => 10, 'pack' => 50],
    ['id' => 444, 'count' => 65, 'price' => 12, 'pack' => 5],
];

$priceList3 = [
    ['id' => 111, 'count' => 100, 'price' => 30, 'pack' => 1],
    ['id' => 222, 'count' => 60, 'price' => 11, 'pack' => 10],
    ['id' => 333, 'count' => 100, 'price' => 13, 'pack' => 50],
];

// Тесты
$result1 = optimizePurchase($priceList1, $N1);
$result2 = optimizePurchase($priceList2, $N1);
$result3 = optimizePurchase($priceList3, $N1);

/* Вывод результатов */
echo "ответ 1: " . json_encode($result1);
echo "ответ 2: " . json_encode($result2);
echo "ответ 3: " . json_encode($result3);
