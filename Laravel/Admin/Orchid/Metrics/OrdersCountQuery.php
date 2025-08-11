<?php

namespace Admin\Orchid\Metrics;

use Admin\Orchid\Models\Order\Order;
use Illuminate\Support\Facades\DB;
use Library\ValueObject\Status;

class OrdersCountQuery
{
    public function todayTotalSum(): int
    {
        $query = <<<SQL
            with order_itmes_1 as (select number, jsonb_array_elements(items) as item
                                   from order__order
                                   where status != 'CANCELLED'
                                     and created_at >= current_date),
                 order_items as (select number, item ->> 'actual_price' as price, item ->> 'quantity' as quantity
                                 from order_itmes_1),
                 orders as (select number, sum(price::int * quantity::int) as sum from order_items group by number)
            select sum(sum)::int as total_sum
            from orders
        SQL;

        return DB::select($query)[0]->total_sum ?? 0;
    }

    public function todayAverageOrdersSum(): int
    {
        $query = <<<SQL
            with order_itmes_1 as (select number, jsonb_array_elements(items) as item
                                   from order__order
                                   where status != 'CANCELLED'
                                     and created_at >= current_date),
                 order_items as (select number, item ->> 'actual_price' as price, item ->> 'quantity' as quantity
                                 from order_itmes_1),
                 orders as (select number, sum(price::int * quantity::int) as sum from order_items group by number)
            select avg(sum)::int as average_sum from orders
            SQL;

        return DB::select($query)[0]->average_sum ?? 0;
    }

    public function countTodayOrders(): int
    {
        return Order::where('created_at', '>=', now()->format('Y-m-d'))
            ->where('status', '!=', Status::Cancelled->value)
            ->count();
    }

    public function countTodayOrdersByStock(): array
    {
        $query = <<<SQL
            with order_itmes_1 as (select number, jsonb_array_elements(items) as item
                                   from order__order
                                   where status != 'CANCELLED'
                                     and created_at >= current_date),
                 order_items as (select number, item ->> 'actual_price' as price, item ->> 'quantity' as quantity
                                 from order_itmes_1),
                 orders_sum as (select number, sum(price::int * quantity::int) as sum from order_items group by number)
            select stock.name as stock_name,
                   sum(orders_sum.sum)::int as total_sum,
                   avg(orders_sum.sum)::int as average_sum,
                   count(stock_id) as quantity
            from (select metadata ->> 'STOCK_ID' as stock_id, number
                  from order__order) as orders_stock
                     inner join price_and_stock_stocks stock on stock.id = orders_stock.stock_id::uuid
                     inner join orders_sum on orders_sum.number = orders_stock.number
            group by stock.name
            order by quantity desc
            SQL;

        return DB::select($query);
    }
}
