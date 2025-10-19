<?php

use App\Models\Message;
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
        Schema::table('messages', function (Blueprint $table) {
            $table->addColumn('boolean', 'is_delivery')->default(false)->after('text');
        });
        $messages = Message::whereNotNull('replied_by')->get();
        foreach ($messages as $message) {
            if (str_contains(strtolower($message->text), 'jastip')) {
                $message->update(['is_delivery' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages' . function (Blueprint $table) {
            $table->dropColumn('is_delivery');
        });
    }
};
