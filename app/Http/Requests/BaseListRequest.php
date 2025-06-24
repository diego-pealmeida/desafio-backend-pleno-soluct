<?php

namespace App\Http\Requests;

use App\Data\ColumnOrderData;
use App\Data\OrdernationData;
use App\Data\PaginationData;

class BaseListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'page'                  => 'nullable|integer|min:0',
            'limit'                 => 'nullable|integer|min:1|max:200',
            'columns_order'         => 'nullable|array',
            'columns_order.*.field' => 'string',
            'columns_order.*.order' => 'string|in:asc,desc'
        ];
    }

    public function toOrdernationData(): OrdernationData
    {
        $columns = [];

        $columnsOrder = $this->input('columns_order') ?: [];

        foreach ($columnsOrder as $columnOrder) {
            $columns[] = new ColumnOrderData($columnOrder['field'], $columnOrder['order']);
        }

        return new OrdernationData($columns);
    }

    public function toPaginationData(): PaginationData
    {
        return new PaginationData($this->input('page', 0), $this->input('limit', 50));
    }
}
