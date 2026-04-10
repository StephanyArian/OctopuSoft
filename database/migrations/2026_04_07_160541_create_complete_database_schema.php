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
        // =====================================================
        // 1. PROFESSIONS
        // =====================================================
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        // =====================================================
        // 2. PLATFORM_NETWORK
        // =====================================================
        Schema::create('platform_network', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('base_url', 255)->nullable();
            $table->timestamps();
        });

        

        // =====================================================
        // 3. USERS
        // =====================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profession_id')->nullable()->constrained('professions')->nullOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->text('biography')->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('website', 255)->nullable();
            $table->boolean('email_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->useCurrent();
            $table->rememberToken();
            $table->timestamps();
            
            // Índices
            $table->index('profession_id');
            $table->index('is_active');
            $table->index('email');
        });

        // =====================================================
// 4. SESSIONS (Estructura estándar de Laravel)
// =====================================================
Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->foreignId('user_id')->nullable()->index();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->longText('payload');
    $table->integer('last_activity')->index();
    $table->timestamps();
});

        // =====================================================
        // 5. PROFESSIONAL_NETWORKS
        // =====================================================
        Schema::create('professional_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('platform_id')->constrained('platform_network')->onDelete('cascade');
            $table->string('username', 150)->nullable();
            $table->string('profile_url', 500);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Índices y unique
            $table->unique(['user_id', 'platform_id']);
            $table->index('user_id');
            $table->index('platform_id');
        });

        // =====================================================
        // 6. PORTFOLIOS
        // =====================================================
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('slug', 120)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_phone')->default(false);
            $table->timestamps();
            
            // Índices
            $table->index('user_id');
            $table->index('is_public');
            $table->index('slug');
        });

        // =====================================================
        // 7. SKILLS
        // =====================================================
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['technical', 'soft']);
            $table->string('name', 150);
            $table->integer('level')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Índices y unique
            $table->unique(['user_id', 'type', 'name']);
            $table->index('user_id');
        });

        // =====================================================
        // 8. EXPERIENCES
        // =====================================================
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['work', 'education']);
            $table->string('institution', 255);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Índices
            $table->index('user_id');
            $table->index('is_visible');
            $table->index('start_date');
        });

        // =====================================================
        // 9. ACHIEVEMENTS
        // =====================================================
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('experience_id')->nullable()->constrained('experiences')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->string('evidence_url', 500)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Índices
            $table->index('user_id');
            $table->index('experience_id');
            $table->index('is_visible');
        });

        // =====================================================
        // 10. TECHNOLOGIES
        // =====================================================
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // =====================================================
        // 11. PROJECTS
        // =====================================================
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('portfolios')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('summary', 300)->nullable();
            $table->text('description')->nullable();
            $table->string('role', 150)->nullable();
            $table->string('demo_url', 500)->nullable();
            $table->string('repository_url', 500)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Índices
            $table->index('portfolio_id');
            $table->index('is_visible');
            $table->index('is_featured');
            $table->index('status');
        });

        // =====================================================
        // 12. PROJECT_SKILL (Pivot table)
        // =====================================================
        Schema::create('project_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->timestamps();
            
            // Unique composite key
            $table->unique(['project_id', 'skill_id']);
        });

        // =====================================================
        // 13. PROJECT_TECHNOLOGY (Pivot table)
        // =====================================================
        Schema::create('project_technology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('technology_id')->constrained('technologies')->onDelete('cascade');
            $table->timestamps();
            
            // Unique composite key
            $table->unique(['project_id', 'technology_id']);
        });
        // =====================================================
        // 14. CACHE (para rate limiting y caché de Laravel)
        // =====================================================
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // =====================================================
        // 15. CACHE_LOCKS (para bloques de caché)
        // =====================================================
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso (respetando dependencias)
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('project_skill');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('professional_networks');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('platform_network');
        Schema::dropIfExists('professions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};