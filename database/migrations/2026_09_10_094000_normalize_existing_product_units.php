<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safe data normalization for existing product records to canonical unit keys
        DB::table('products')->whereIn('unit', [
            'قطعه', 'قطعة', 'قطع', 'حبة', 'حبه', 'Piece', 'piece', 'pieces', 'pc', 'pcs', 'pieza', 'piezas', 'stk', 'stück', 'stueck', 'adet', 'tane', '件', '个', '個'
        ])->update(['unit' => 'piece']);

        DB::table('products')->whereIn('unit', [
            'كغ', 'كيلو', 'كيلوجرام', 'كجم', 'Kg', 'kg', 'kilogram', 'kilograms', 'kilo', 'quilogramo', 'kilogramm', '公斤', '千克'
        ])->update(['unit' => 'kg']);

        DB::table('products')->whereIn('unit', [
            'كرتون', 'كرتونة', 'كرتونه', 'Carton', 'carton', 'cartons', 'ctn', 'cartón', 'karton', 'koli', '箱'
        ])->update(['unit' => 'carton']);

        DB::table('products')->whereIn('unit', [
            'علبة', 'علبه', 'صندوق', 'Box', 'box', 'boxes', 'caja', 'cajas', 'schachtel', 'kutu', '盒'
        ])->update(['unit' => 'box']);

        DB::table('products')->whereIn('unit', [
            'متر', 'أمتار', 'امتار', 'Meter', 'meter', 'meters', 'm', 'metro', 'metros', 'metre', '米'
        ])->update(['unit' => 'meter']);

        DB::table('products')->whereIn('unit', [
            'طقم', 'أطقم', 'اطقم', 'مجموعة', 'Set', 'set', 'sets', 'juego', 'takım', 'takim', '套'
        ])->update(['unit' => 'set']);

        DB::table('products')->whereIn('unit', [
            'طن', 'أطنان', 'اطنان', 'Ton', 'ton', 'tons', 't', 'tonelada', 'tonne', '吨'
        ])->update(['unit' => 'ton']);

        DB::table('products')->whereIn('unit', [
            'لتر', 'ليتر', 'Liter', 'liter', 'liters', 'l', 'litro', 'litre', '升'
        ])->update(['unit' => 'liter']);

        // Fallback any remaining non-canonical or empty unit to piece
        DB::table('products')
            ->whereNotIn('unit', ['piece', 'kg', 'carton', 'box', 'meter', 'set', 'ton', 'liter'])
            ->orWhereNull('unit')
            ->update(['unit' => 'piece']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data normalization does not require reversal
    }
};
