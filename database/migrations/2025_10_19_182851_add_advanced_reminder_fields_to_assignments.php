<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvancedReminderFieldsToAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->datetime('first_reminder_at')->nullable()->after('reminder_frequency');
            $table->datetime('stop_reminders_at')->nullable()->after('first_reminder_at');
            $table->datetime('last_reminder_sent_at')->nullable()->after('stop_reminders_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['first_reminder_at', 'stop_reminders_at', 'last_reminder_sent_at']);
        });
    }
}
