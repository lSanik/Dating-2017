<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTicketStatusToTicketMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->integer('ticket_status_id')->unsigned()->nullable();
            $table->index('ticket_status_id');
            $table->foreign('ticket_status_id')
                ->references('id')->on('ticket_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('ticket_status_id');
            $table->dropIndex('ticket_messages_ticket_status_id_index');
            $table->dropForeign('ticket_messages_ticket_status_id_foreign');
        });
    }
}
