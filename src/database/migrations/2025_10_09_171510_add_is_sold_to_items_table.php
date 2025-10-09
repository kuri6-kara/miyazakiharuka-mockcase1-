<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSoldToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // boolean型のis_soldカラムを追加し、デフォルト値をfalseに設定。
            // 既存のdescriptionカラムの後ろに配置します。
            $table->boolean('is_sold')->default(false)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            // ロールバック（down）時には、is_soldカラムを削除します。
            $table->dropColumn('is_sold');
        });
    }
}
