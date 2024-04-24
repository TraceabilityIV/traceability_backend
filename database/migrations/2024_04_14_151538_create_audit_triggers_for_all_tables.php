<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Función para crear el trigger
        DB::statement('
       CREATE OR REPLACE FUNCTION trigger_audit_function()
       RETURNS TRIGGER AS $$
       BEGIN
           IF (TG_OP = \'INSERT\') THEN
               INSERT INTO audit_log (table_name, record_id, old_data, new_data, event, user_name, ip_address, created_at)
               VALUES (TG_TABLE_NAME, CASE WHEN NEW.id IS NOT NULL THEN NEW.id ELSE NULL END, NULL, row_to_json(NEW), \'create\', session_user, inet_client_addr(), current_timestamp);
               RETURN NEW;
           ELSIF (TG_OP = \'UPDATE\') THEN
               INSERT INTO audit_log (table_name, record_id, old_data, new_data, event, user_name, ip_address, created_at)
               VALUES (TG_TABLE_NAME, CASE WHEN OLD.id IS NOT NULL THEN OLD.id ELSE NULL END, row_to_json(OLD), row_to_json(NEW), \'update\', session_user, inet_client_addr(), current_timestamp);
               RETURN NEW;
           ELSIF (TG_OP = \'DELETE\') THEN
               INSERT INTO audit_log (table_name, record_id, old_data, new_data, event, user_name, ip_address, created_at)
               VALUES (TG_TABLE_NAME, CASE WHEN OLD.id IS NOT NULL THEN OLD.id ELSE NULL END, row_to_json(OLD), NULL, \'delete\', session_user, inet_client_addr(), current_timestamp);
               RETURN OLD;
           END IF;
           RETURN NULL;
       END;
       $$ LANGUAGE plpgsql;
   ');

        // Crear el trigger en todas las tablas
        $tables = DB::select('SELECT table_name, column_name FROM information_schema.columns WHERE table_schema = \'public\' AND column_name = \'id\' GROUP BY table_name, column_name');

        foreach ($tables as $table) {
            // Crear el trigger
            if ($table->table_name != 'audit_log' && $table->table_name != 'migrations' && $table->table_name != 'failed_jobs' && $table->table_name != 'jobs' && $table->table_name != 'auditoria' && $table->table_name != 'notifications' && $table->table_name != 'usuarios_has_roles')
                DB::statement("
                CREATE TRIGGER audit_trigger
                AFTER INSERT OR UPDATE OR DELETE ON {$table->table_name}
                FOR EACH ROW EXECUTE PROCEDURE trigger_audit_function();
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar los triggers de auditoría
        $tables = DB::select('SELECT table_name, column_name FROM information_schema.columns WHERE table_schema = \'public\' AND column_name = \'id\' GROUP BY table_name, column_name');

        foreach ($tables as $table) {
            if ($table->table_name != 'audit_log' && $table->table_name != 'migrations' && $table->table_name != 'failed_jobs' && $table->table_name != 'jobs' && $table->table_name != 'auditoria' && $table->table_name != 'notifications' && $table->table_name != 'usuarios_has_roles')
                DB::statement("DROP TRIGGER IF EXISTS audit_trigger ON {$table->table_name}");
        }

        // Eliminar la función de trigger
        DB::statement('DROP FUNCTION IF EXISTS trigger_audit_function');
    }
};
