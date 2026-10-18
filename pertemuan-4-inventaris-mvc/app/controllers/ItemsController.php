<?php
class ItemsController extends Controller{
    private function payload(array $input): array {
        return [
        'code' => $input['code'],
        'name' => $input['name'],
        'category_id' => ($input['category_id'] ?? '') !== '' ? (int)$input['category_id'] : null,
            'quantity'    => (int)($input['quantity'] ?? 0),
            'unit'        => $input['unit'] ?: 'unit',
            'price'       => (float)($input['price'] ?? 0),
            'entry_date'  => ($input['entry_date'] ?? '') !== '' ? $input['entry_date'] : null,
            'description' => $input['description'] ?? '',
        ];
    }
}