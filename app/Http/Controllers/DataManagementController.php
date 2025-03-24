<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Session;

class DataManagementController extends Controller
{
    // Tables that should never be cleaned
    private $protectedTables = [
        'business_hours',
        'departments',
        'department_user', 
        'industries',
        'migrations',
        'permission_role',
        'permissions',
        'role_user',
        'roles',
        'settings',
        'statuses'
    ];

    /**
     * Get all foreign key constraints from the database
     *
     * @return array Array of constraints with their table relationships
     */
    private function getDatabaseConstraints()
    {
        $constraints = [];
        
        $results = DB::select("
            SELECT 
                TABLE_NAME as table_name,
                COLUMN_NAME as column_name,
                REFERENCED_TABLE_NAME as referenced_table_name,
                REFERENCED_COLUMN_NAME as referenced_column_name
            FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                REFERENCED_TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($results as $result) {
            if (!isset($constraints[$result->table_name])) {
                $constraints[$result->table_name] = [];
            }
            
            $constraints[$result->table_name][] = $result->referenced_table_name;
        }

        return $constraints;
    }

    /**
     * Display table cleaning interface
     */
    public function cleaning()
    {
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(function($table) {
                $table = (array) $table;
                return reset($table); // Get the table name from the object
            })
            ->filter(function($table) {
                return !in_array($table, $this->protectedTables);
            })
            ->values()
            ->all();

        return view('data.cleaning', compact('tables'));
    }

    /**
     * Display data import interface
     */
    public function import()
    {
        return view('data.import');
    }

    /**
     * Display data generation interface
     */
    public function generation()
    {
        return view('data.generation');
    }

    /**
     * Process table cleaning
     */
    public function cleanTables(Request $request)
    {
        if (!$request->has('tables')) {
            Session::flash('flash_message_warning', __('Please select at least one table to clean'));
            return redirect()->back();
        }

        $selectedTables = $request->tables;
        $orderedTables = $this->calculateDeletionOrder($selectedTables);
        
        DB::beginTransaction();
        
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($orderedTables as $table) {
                if (in_array($table, $this->protectedTables)) {
                    continue;
                }

                if ($table === 'users') {
                    // Keep admin users
                    DB::table('users')
                        ->whereNotExists(function($query) {
                            $query->select(DB::raw(1))
                                ->from('role_user')
                                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                                ->whereRaw('role_user.user_id = users.id')
                                ->where('roles.name', '=', 'administrator');
                        })
                        ->delete();
                } else if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }

                // Reset auto-increment to 1 for the table
                if (Schema::hasTable($table)) {
                    try {
                        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                    } catch (\Exception $e) {
                        // Si la table n'a pas d'auto-increment, on ignore l'erreur
                        continue;
                    }
                }
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
            Session::flash('flash_message', __('Selected tables cleaned successfully and auto-increments reset!'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            Session::flash('flash_message_warning', __('An error occurred while cleaning the tables: ') . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Calculate correct order for table deletion to respect foreign key constraints
     *
     * @param array $selectedTables Tables to be ordered
     * @return array Ordered tables for deletion
     */
    private function calculateDeletionOrder(array $selectedTables)
    {
        $constraints = $this->getDatabaseConstraints();
        $result = [];
        $availableTables = array_flip($selectedTables);
        
        while (!empty($availableTables)) {
            $progress = false;
            
            foreach (array_keys($availableTables) as $table) {
                $canProcess = true;
                
                // Check if this table has any dependencies on other selected tables
                if (isset($constraints[$table])) {
                    foreach ($constraints[$table] as $referencedTable) {
                        if (isset($availableTables[$referencedTable])) {
                            $canProcess = false;
                            break;
                        }
                    }
                }
                
                if ($canProcess) {
                    $result[] = $table;
                    unset($availableTables[$table]);
                    $progress = true;
                }
            }
            
            // Handle circular dependencies
            if (!$progress && !empty($availableTables)) {
                foreach (array_keys($availableTables) as $table) {
                    $result[] = $table;
                }
                break;
            }
        }
        
        return array_reverse($result);
    }

    /**
     * Process data import
     */
    public function importData(Request $request)
    {
        // Implement data import logic here
        Session::flash('flash_message', 'Data imported successfully!');
        return redirect()->back();
    }

    /**
     * Process data generation
     */
    public function generateData(Request $request)
    {
        // Implement data generation logic here
        Session::flash('flash_message', 'Data generated successfully!');
        return redirect()->back();
    }
}