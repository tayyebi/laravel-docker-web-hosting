<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MapPlanIdToWebsites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->after('domain_id');
            $table->dropColumn('dockerfile');
            // $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('websites', function (Blueprint $table) {
            // Remove the plan_id column
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');

            // Re-add the dockerfile column
            $table->string('dockerfile')->nullable();
        });
    }
}
