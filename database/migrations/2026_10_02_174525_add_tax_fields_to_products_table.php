<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            /**
             * tax_code: código del tributo según Factus
             *   '01' = IVA (el más común)
             *   '04' = Impuesto al consumo
             *   '35' = Impuesto a ultraprocesados
             *
             * tax_rate: porcentaje del impuesto
             *   Para IVA los valores comunes son: 19.00, 5.00, 0.00
             *
             * is_tax_excluded: true si el producto está excluido de impuestos
             *   (ej: medicamentos, alimentos básicos de la canasta familiar)
             */
            $table->string('tax_code', 5)->default('01')->after('is_active');
            $table->decimal('tax_rate', 5, 2)->default(19.00)->after('tax_code');
            $table->boolean('is_tax_excluded')->default(false)->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
