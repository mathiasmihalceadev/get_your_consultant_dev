<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('feedback_sent_at')->nullable()->after('processed_at');
        });

        Schema::create('report_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('most_useful_info');
            $table->text('wanted_extra')->nullable();
            $table->boolean('would_recommend');
            $table->text('trust_improvement')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_feedback');

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('feedback_sent_at');
        });
    }
};
