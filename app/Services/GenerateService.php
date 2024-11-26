<?php

namespace App\Services;
use App\Services\Interfaces\GenerateServiceInterface;
use App\Repositories\GenerateRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Class GenerateService
 * @package App\Services
 */
class GenerateService extends BaseService implements GenerateServiceInterface
{   
    protected $generateRepository;

    public function __construct(GenerateRepository $generateRepository) {
        $this->generateRepository = $generateRepository;
    }

    public function paginate($request) {
        $condition['keyword'] = addslashes($request -> input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $generate = $this->generateRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perpage, 
            ['path' => 'generate/index'], 
        );
        return $generate;
    }

    public function create($request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
        try {
            $database = $this->makeDatabase($request); 
            $controller =  $this->makeController($request);
            $model = $this->makeModel($request);
            $repository = $this->makeRepository($request);
            $service = $this->makeService($request);
            $provider = $this->makeProvider($request);
            $makeRequest = $this->makeRequest($request);
            $view = $this->makeView($request);
            if ($request->input("module_type") == 'catalogue') {
                $this->makeRule($request);
            }
            $routes = $this->makeRoutes($request);

            // Lấy tất cả dữ liệu từ request
            // $payload = $request->except('_token', 'send');
            // $payload['user_id'] = Auth::id();
            // $this->generateRepository->create($payload);

            // DB::commit(); // Nếu không có lỗi, commit giao dịch
            // return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function update($id, $request) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            // Lấy tất cả dữ liệu từ request
            $payload = $request->except('_token', 'send');
            $this->generateRepository->update($id, $payload);

            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    public function delete($id) {
        DB::beginTransaction(); // Bắt đầu một giao dịch
    
        try {
            $this->generateRepository->delete($id);
            DB::commit(); // Nếu không có lỗi, commit giao dịch
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Nếu có lỗi, rollback giao dịch
            // In ra lỗi và dừng thực thi (thường chỉ dùng trong quá trình phát triển)
            echo $e->getMessage();
            die();
        }
    }

    private function makeDatabase($request)
    {
        try {
            $payload = $request->only("schema", 'name', 'module_type');
            $module = $this->convertModuleNameToTableName($payload['name']);
            $moduleExtract = explode('_', $module);
            $tableName = $module . 's';

            // Tạo file migration cho bảng chính
            $this->createMainTableMigration($payload['schema'], $tableName);

            // Tạo migration cho bảng pivot nếu module_type khác 'difference'
            if ($payload['module_type'] !== 'difference') {
                $this->createPivotTableMigration($module, $tableName);

                // Tạo migration cho bảng liên quan (relation) nếu cần
                // dd([$moduleExtract[0] . '_catalogues', $moduleExtract[0] . 's']);
                if (count($moduleExtract) == 1) {
                    $this->createRelationTableMigration($module, $moduleExtract);
                }
            }

            ARTISAN::call('migrate');
            return true;
        } catch (Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi
            echo $e->getMessage(); // In ra lỗi (dùng trong quá trình phát triển)
            die();
        }
    }

    private function makeController($request) {
        try {
            switch($request->input('module_type')) {
                case 'catalogue':
                    $this->createTemplateController($request, 'TemplateCatalogueController');
                    break;
                case 'detail':
                    $this->createTemplateController($request, 'TemplateController');
                    break;
                default:
                    echo 1; die();
            }
        } catch(Exception $e) {
            echo $e->getMessage(); die();
            return false;
        }
    }

    private function makeModel($request) {
        try {
            $moduleType = $request->input('module_type');
            switch($moduleType) {
                case 'catalogue':
                    $this->createModelCatalogueTemplate($request);
                    break;
                case 'detail':
                    $this->createModelTemplate($request);
                    break;
            }
        } catch(Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function makeRepository($request) {
        return $this->makeLayer($request, 'Repositories', 'CatalogueRepository', 'Repository');
    }
    
    private function makeService($request) {
        return $this->makeLayer($request, 'Services', 'CatalogueService', 'Service');
    }
    
    private function makeProvider($request) {
        try {
            $name = $request->input('name');
            $provider = [
                'providerPath' => base_path('app/Providers/AppServiceProvider.php'),
                'repoitoryProviderPath' => base_path('app/Providers/RepositoryServiceProvider.php')
            ];

            foreach($provider as $key => $val) {
                $content = file_get_contents($val);
                $insertLine = ($key == 'providerPath') ? "'App\\Services\\Interfaces\\{$name}ServiceInterface' => 'App\\Services\\{$name}Service'," : "'App\\Repositories\\Interfaces\\{$name}RepositoryInterface' => 'App\\Repositories\\{$name}Repository',";

                $position = strpos($content, '];');
                if ($position !== false) {
                    $newContent = substr_replace($content, "    ".$insertLine. "\n". '    ', $position, 0);
                }
                File::put($val, $newContent);
            }
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            return false;
        }
    }

    private function makeRequest($request) {
        try {
            $name = $request->input('name');
            $requestArray = [
                'Store' . $name . 'Request.php',
                'Update' . $name . 'Request.php',
                'Delete' . $name . 'Request.php',
            ];

            $requestTemplate = [
                'RequestTemplateStore.php',
                'RequestTemplateUpdate.php',
                'RequestTemplateDelete.php',
            ];

            if ($request->input('module_type') != 'catalogue') {
                // Hàm xóa 1 biến hoặc 1 phần tử
                unset($requestArray[2]);
                unset($requestTemplate[2]);
            }

            foreach($requestTemplate as $key => $val) {
                $requestPath = base_path('app/Templates/Requests/' . $val);
                $requestContent = file_get_contents($requestPath);
                $requestContent = str_replace('{Module}', $name, $requestContent);
                $requestPut = base_path('app/Http/Request/' . $requestArray[$key]);
                File::put($requestPut, $requestContent);

            }
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            return false;
        }
    }

    private function makeView($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $extractModule = explode('_', $module);
            $basePath = resource_path("views/backend/{$extractModule[0]}");

            $folderPath = (count($extractModule) == 2) ? "$basePath/{$extractModule[1]}" : "$basePath/{$extractModule[0]}";
            $componentPath = "$folderPath/component/";
            
            $this->createDirectory($folderPath);
            $this->createDirectory($componentPath);

            $sourcePath = base_path('app/Templates/views/' .  ((count($extractModule) == 2) ? $extractModule[1] : "module") . '/');
            $viewPath = (count($extractModule) == 2) ? "{$extractModule[0]}.{$extractModule[1]}" : "{$extractModule[0]}.{$extractModule[0]}";
            $replace = [
                'view' => $viewPath,
                'module' => lcfirst($name),
                'Module' => $name,
            ];

            $fileArray = ['store.blade.php', 'index.blade.php', 'delete.blade.php'];
            $componentFile = ['aside.blade.php', 'filter.blade.php', 'table.blade.php'];
            $this->copyAndReplaceContent($sourcePath, $folderPath, $fileArray, $replace);
            $this->copyAndReplaceContent($sourcePath. 'component/', $componentPath, $componentFile, $replace);
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            return false;
        }
    }

    private function makeRoutes($request) {
        try {
            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $moduleExtract = explode('_', $module);
            $routesPath = base_path('routes/web.php');
            $content = file_get_contents($routesPath);
            $routeUrl = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}/{$moduleExtract[1]}" : "{$moduleExtract[0]}";
            $routeName = (count($moduleExtract) == 2) ? "{$moduleExtract[0]}.{$moduleExtract[1]}" : "{$moduleExtract[0]}";

            $routeGroup = <<<ROUTE
            Route::prefix('{$routeUrl}') -> group(function() {
                    Route::get('/index', [{$name}Controller::class, 'index']) -> name('{$routeName}.index');
                    Route::get('/create', [{$name}Controller::class, 'create']) -> name('{$routeName}.create');
                    Route::post('/store', [{$name}Controller::class, 'store']) -> name('{$routeName}.store');
                    Route::get('{id}/edit', [{$name}Controller::class, 'edit']) -> name('{$routeName}.edit') -> where(['id' => '[0-9]+']);
                    Route::post('{id}/update', [{$name}Controller::class, 'update']) -> name('{$routeName}.update') -> where(['id' => '[0-9]+']);
                    Route::get('{id}/delete', [{$name}Controller::class, 'delete']) -> name('{$routeName}.delete') -> where(['id' => '[0-9]+']);
                    Route::delete('{id}/destroy', [{$name}Controller::class, 'destroy']) -> name('{$routeName}.destroy') -> where(['id' => '[0-9]+']);
                });

                // @@newModule@@
            ROUTE;

            $useController = <<<ROUTE
            use App\\Http\\Controllers\\Backend\\{$name}Controller;
            // @@useController@@
            ROUTE;

            $content = str_replace('// @@newModule@@', $routeGroup, $content);
            $content = str_replace('// @@useController@@', $useController, $content);
            FILE::put($routesPath, $content);
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            return false;
        }
    }

    private function makeRule($request) {
        try {
            $name = $request->input('name');
            $destination = base_path("app/Rules/Check" . $name . "ChildrenRule.php");
            $ruleTemplate = base_path("app/Templates/RuleTemplate.php");
            $content = file_get_contents($ruleTemplate);
            $content = str_replace("{Module}", $name, $content);
            if (!File::exists($destination)) {
                File::put($destination, $content);
            }
            return true;
        } catch (Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            return false;
        }
    }

    private function createDirectory($path) {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    private function copyAndReplaceContent(string $sourcePath, string $folderPath, array $fileArray, array $replace) {
        foreach ($fileArray as $key => $val) {
            $sourceFile = $sourcePath. $val;
            $destination = "{$folderPath}/{$val}";
            $content = file_get_contents($sourceFile);
            foreach($replace as $key => $val) {
                $content = str_replace('{'.$key.'}', $val, $content);
            }

            if (!File::exists($destination)) {
                File::put($destination, $content);
            }
        }
    }

    private function initializeServiceLayer($layer = '', $folder = '', $request) {
        $name = $request->input('name');
        $module = $this->convertModuleNameToTableName($name);
        $moduleExtract = explode('_', $module);
        $name = (count($moduleExtract) == 2 ) ? ucfirst($moduleExtract[0]) : $request->input('name');

        $option = [
            $layer . 'Name' => $name . $layer,
            $layer . 'InterfaceName' => $name . $layer . 'Interface'
        ];
        
        $layerInterfaceRead = base_path('app/Templates/'. $folder . '/' . 'Template'. $layer .'Interface.php');
        $layerInterfaceContent = file_get_contents($layerInterfaceRead);
        $layerInterfacePathPut = base_path('app/'. $folder .'/Interfaces/' .$option[$layer . 'InterfaceName'] .'.php');

        $layerPathRead = base_path('app/Templates/'. $folder . '/' . 'Template'. $layer .'.php');
        $layerContent = file_get_contents($layerPathRead);
        $layerPathPut = base_path('app/' . $folder . '/' . $option[$layer . 'Name'] . '.php');

        return [
            'interface' => [
                'layerInterfaceContent' => $layerInterfaceContent,
                'LayerInterfacePath' => $layerInterfacePathPut,
            ],
            'service' => [
                'layerContent' => $layerContent,
                'layerPathPut' => $layerPathPut
            ]
        ];
    }

    // Make ModelTemplate Start
    private function createModelCatalogueTemplate($request) {
        $this->generateModelFromTemplate($request, 'ModelCatalogueLanguage.php');
        return $this->generateModelFromTemplate($request, 'ModelCatalogue.php');
    }

    private function createModelTemplate($request) {
        return $this->generateModelFromTemplate($request, 'Model.php');
    }

    private function generateModelFromTemplate($request, $templateFile) {
        $modelName = $request->input('name') . '.php';
        // Lấy đường dẫn file
        $templateModelPath = base_path('app/Templates/Models/' . $templateFile);
        // Đọc nội dung trong file
        $modelContent = file_get_contents($templateModelPath);
        $module = $this->convertModuleNameToTableName($request->input('name'));
        $extractModule = explode('_', $module);

        $replace = [
            "Module" => ucfirst($extractModule[0]),
            'module' => lcfirst($extractModule[0]) 
        ];

        foreach($replace as $key => $val) {
            $modelContent = str_replace('{'.$key.'}', $val, $modelContent);
        }

        $modelPath = base_path('app/Models/' . $modelName);
        FILE::put($modelPath, $modelContent);
        return true;
    }
    // Make ModelTemplate End

    private function createTemplateController($request, $controllerFile) {
        try {
            // ProductController
            $controllerName = $request->input('name'). 'Controller.php';
            // Lấy đường dẫn file
            $templateControllerPath = base_path('app/Templates/Controllers/' .$controllerFile . '.php');
            // Đọc nội dung trong file
            $controllerContent = file_get_contents($templateControllerPath);

            $name = $request->input('name');
            $module = $this->convertModuleNameToTableName($name);
            $extractModule = explode('_', $module);
            $name = (count($extractModule) == 2 ) ? ucfirst($extractModule[0]) : $request->input('name');
            $replace = [
                "Module" => ucfirst($extractModule[0]),
                'module' => lcfirst($extractModule[0]) 
            ];

            foreach($replace as $key => $val) {
                $controllerContent = str_replace('{'.$key.'}', $val, $controllerContent);
            }

            $controllerPath = base_path('app/Http/Controllers/Backend/' . $controllerName);
            FILE::put($controllerPath, $controllerContent);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    private function makeLayer($request,  $layerFolder, $catalogueLayer, $detailLayer) {
        try {
            $moduleType = $request->input('module_type');
            if ($moduleType !== 3) {
                $name = $request->input('name');
                $module = $this->convertModuleNameToTableName($name);
                $moduleExtract = explode('_', $module);
    
                $layer = $this->initializeServiceLayer(
                    $moduleType === 'catalogue' ? $catalogueLayer : $detailLayer,
                    $layerFolder,
                    $request
                );
    
                $this->replaceAndWriteLayerContent($layer, $name, $moduleExtract);
    
                return true;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    private function replaceAndWriteLayerContent($layer, $name, $moduleExtract) {
        $interfaceContent = str_replace('{Module}', $name, $layer['interface']['layerInterfaceContent']);
        FILE::put($layer['interface']['LayerInterfacePath'], $interfaceContent);
        
        $replace = [
            'Module' => ucfirst($moduleExtract[0]),
            'module' => lcfirst($moduleExtract[0]),
        ];
        
        $layerContent = $layer['service']['layerContent'];
        foreach ($replace as $key => $val) {
            $layerContent = str_replace('{' . $key . '}', $val, $layerContent);
        }
        
        FILE::put($layer['service']['layerPathPut'], $layerContent);
    }

    // Make Database Start
    private function areBothMigrationFilesExist(array $tableNames) {
        $migrationFiles = File::files(database_path('migrations/'));

        $filesFound = 0;

        foreach ($migrationFiles as $file) {
            foreach ($tableNames as $tableName) {
                if (strpos($file->getFilename(), 'create_' . $tableName . '_table.php') !== false) {
                    $filesFound++;
                    break; // Nếu tìm thấy thì thoát khỏi vòng lặp của tableName
                }
            }
        }

        // Chỉ trả về true khi cả 2 file đều tồn tại
        return $filesFound === count($tableNames);
    }

    private function createMainTableMigration($schema, $tableName) {
        $payloadMigration = [
            'schema' => $schema,
            'name' => $tableName
        ];

        $migrationFileName = date("Y_m_d_His"). '_create_' . $tableName . '_table.php';
        $migrationPath = database_path('migrations/'. $migrationFileName);
        $migrationTemplate = $this->createMigrationFile($payloadMigration, $tableName);

        File::put($migrationPath, $migrationTemplate);
    }

    private function createPivotTableMigration($module, $tableName) {
        $foreignKey = $module . '_id';
        $pivotTableName = $module . '_language';

        $payloadSchema = [
            'schema' => $this->pivotSchema($tableName, $foreignKey, $pivotTableName),
            'name' => $pivotTableName,
        ];

        $migrationPivotFileName = date("Y_m_d_His", time() + 10). '_create_' . $payloadSchema['name'] . '_table.php';
        $migrationPivotPath = database_path('migrations/'. $migrationPivotFileName);
        $migrationPivotTemplate = $this->createMigrationFile($payloadSchema, $pivotTableName);

        File::put($migrationPivotPath, $migrationPivotTemplate);
    }

    private function createRelationTableMigration($module, $moduleExtract) {
        $dropRelationTable = $module . '_catalogue_' . $moduleExtract[0]; // product_catalogue_product
        $payloadRelation = [
            'schema' => $this->relationSchema($dropRelationTable, $moduleExtract),
            'name' => $dropRelationTable,
        ];

        $migrationRelationFileName = date("Y_m_d_His", time() + 20). '_create_' . $payloadRelation['name'] . '_table.php';
        $migrationRelationPath = database_path('migrations/'. $migrationRelationFileName);
        $migrationRelationTemplate = $this->createMigrationFile($payloadRelation, $dropRelationTable);

        File::put($migrationRelationPath, $migrationRelationTemplate);
    }
    private function relationSchema($tableName = '', $moduleExtract) {
        $schema = <<<SCHEMA
            Schema::create('{$tableName}', function (Blueprint \$table) {
                \$table->unsignedBigInteger('{$moduleExtract[0]}_catalogue_id');
                \$table->unsignedBigInteger('{$moduleExtract[0]}_id');
                \$table->foreign('{$moduleExtract[0]}_catalogue_id')->references('id')->on('{$moduleExtract[0]}_catalogues')->onDelete('cascade');
                \$table->foreign('{$moduleExtract[0]}_id')->references('id')->on('{$moduleExtract[0]}s')->onDelete('cascade');
            });
        SCHEMA;
        return $schema;
    }

    private function pivotSchema($tableName = '', $foreignKey = '', $pivotTableName) {
        $pivotSchema = <<<SCHEMA
            Schema::create('{$pivotTableName}', function (Blueprint \$table) {
                \$table->id();
                \$table->unsignedBigInteger('{$foreignKey}');
                \$table->unsignedBigInteger('language_id');
                \$table->foreign('{$foreignKey}')->references('id')->on('{$tableName}')->onDelete('cascade');
                \$table->foreign('language_id')->references('id')->on('language')->onDelete('cascade');
                \$table->string('name');
                \$table->text('description')->nullable();
                \$table->longText('content')->nullable();
                \$table->string('meta_title')->nullable();
                \$table->string('meta_keyword')->nullable();
                \$table->text('meta_description')->nullable();
                \$table->string('canonical')->nullable();
                \$table->timestamps();
            });
        SCHEMA;
        return $pivotSchema;
    }
    
    private function createMigrationFile($payload, $dropTable = '') {
        // Đây là 1 heredoc
        return <<<MIGRATION
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                /**
                 * Run the migrations.
                 *
                 * @return void
                 */
                public function up()
                {
                   $payload[schema]
                }

                /**
                 * Reverse the migrations.
                 *
                 * @return void
                 */
                public function down()
                {   
                    Schema::dropIfExists('{$dropTable}');
                }
            };
        MIGRATION;
    }
    // Make Database Ends
    private function convertModuleNameToTableName($name) {
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $temp;
    }

    private function paginateSelect() {
        return ['id', 'name', 'schema'];
    }
}
