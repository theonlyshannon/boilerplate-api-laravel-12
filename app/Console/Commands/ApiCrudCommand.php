<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApiCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apiv1 {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD files: model, migration, controller, store request, update request';

    public function handle()
    {
        $this->info('Creating magic... 🪄');

        $this->createModel();

        $this->createController();

        $this->createRequests();

        $this->modifyMigration();

        $this->createResource();

        $this->modifyRepository();
        $this->info('Repository created successfully! ✅');

        $this->createFactory();;

        $this->addRoutes();

        $this->comment('Api Make Successful');
    }

    protected function createModel()
    {
        $name = $this->argument('name');
        $this->call('make:model', ['name' => $name, '-m' => true]);

        $modelPath = app_path("Models/{$name}.php");
        $modelContent = <<<EOT
            <?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Factories\HasFactory;
            use Illuminate\Database\Eloquent\Model;
            use Illuminate\Database\Eloquent\SoftDeletes;
            use App\Traits\UUID;

            class {$name} extends Model
            {
                use HasFactory, UUID, SoftDeletes;

                protected \$fillable = [
                    // Add your columns here
                ];
            }
            EOT;

        file_put_contents($modelPath, $modelContent);
    }

    protected function createRequests()
    {
        $name = $this->argument('name');
        $this->call('make:request', ['name' => "{$name}StoreRequest"]);
        $this->call('make:request', ['name' => "{$name}UpdateRequest"]);

        $storeRequestPath = app_path("Http/Requests/{$name}StoreRequest.php");
        $storeRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Store{$name}Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($storeRequestPath, $storeRequestContent);

        $updateRequestPath = app_path("Http/Requests/{$name}UpdateRequest.php");
        $updateRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Update{$name}Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($updateRequestPath, $updateRequestContent);
    }

    protected function createController()
    {
        $name = $this->argument('name');
        $this->call('make:controller', ['name' => "Store{$name}Request"]);

        $controllerPath = app_path("Http/Controllers/Api/{$name}Controller.php");

        $controllerContent =
            <<<'EOT'
            <?php

            namespace App\Http\Controllers\Api;

            use App\Helpers\HashidsHelper;
            use App\Helpers\ResponseHelper;
            use App\Http\Controllers\Controller;
            use App\Http\Requests\__namePascalCase__StoreRequest;
            use App\Http\Requests\__namePascalCase__UpdateRequest;
            use App\Http\Resources\__namePascalCase__Resource;
            use App\Http\Resources\PaginateResource;
            use App\Interfaces\__namePascalCase__RepositoryInterface;
            use Illuminate\Http\Request;
            use Illuminate\Support\Str;

            class __namePascalCase__Controller extends Controller
            {
                protected $__nameCamelCase__Repository;

                public function __construct(__namePascalCase__RepositoryInterface $__nameCamelCase__Repository)
                {
                    $this->__nameCamelCase__Repository = $__nameCamelCase__Repository;

                    $this->middleware('permission:__nameKebabCase__-list', ['only' => ['index', 'getAllPaginated', 'getAllActive', 'show']]);
                    $this->middleware('permission:__nameKebabCase__-create', ['only' => ['store']]);
                    $this->middleware('permission:__nameKebabCase__-edit', ['only' => ['update']]);
                    $this->middleware('permission:__nameKebabCase__-delete', ['only' => ['destroy']]);
                }

                public function index(Request $request)
                {
                    $request->merge([
                        'search' => $request->has('search') ? $request->search : null,
                        'limit' => $request->has('limit') ? $request->limit : null,
                    ]);

                    $request = $request->validate([
                        'search' => 'nullable|string',
                        'limit' => 'nullable|integer|min:1',
                    ]);

                    try {
                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAll(
                            search: $request['search'],
                            limit: $request['limit'],
                            execute: true
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', __namePascalCase__Resource::collection($__nameCamelCasePlurals__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function getAllPaginated(Request $request)
                {
                    $request->merge([
                        'search' => $request->has('search') ? $request->search : null,
                        'rowsPerPage' => $request->has('rowsPerPage') ? $request->rowsPerPage : null,
                    ]);

                    $request = $request->validate([
                        'search' => 'nullable|string',
                        'rowsPerPage' => 'required|integer',
                    ]);

                    try {
                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAllPaginated(
                            search: $request['search'],
                            rowsPerPage: $request['rowsPerPage']
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', PaginateResource::make($__nameCamelCasePlurals__, __namePascalCase__Resource::class), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function show($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->getById(
                            id: HashidsHelper::decodeId($id),
                            withTrashed: false
                        );

                        return ResponseHelper::jsonResponse(true, 'Success', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function store(__namePascalCase__StoreRequest $request)
                {
                    $request = $request->validated();

                    try {

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->create($request);

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil ditambahkan.', new __namePascalCase__Resource($__nameCamelCase__), 201);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function update(__namePascalCase__UpdateRequest $request, $id)
                {
                    $request = $request->validated();

                    try {

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->update(
                            data: $request,
                            id: HashidsHelper::decodeId($id),
                        );

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil diubah.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function destroy($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->delete(HashidsHelper::decodeId($id));

                        return ResponseHelper::jsonResponse(true, 'Data __nameProperCase__ berhasil dihapus.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }
            }
            EOT;

        $controllerContent = str_replace('__namePascalCase__', $name, $controllerContent);
        $controllerContent = str_replace('__nameCamelCase__', Str::camel($name), $controllerContent);
        $controllerContent = str_replace('__nameSnakeCase__', Str::snake($name), $controllerContent);
        $controllerContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $controllerContent);
        $controllerContent = str_replace('__nameKebabCase__', Str::kebab($name), $controllerContent);
        $controllerContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $controllerContent);

        file_put_contents($controllerPath, $controllerContent);
    }

    protected function modifyMigration()
    {
        $name = $this->argument('name');
        $this->call('make:migration', ['name' => "create_{$name}_table"]);
        $name = Str::snake($name);
        $name = Str::plural($name);
        $migration = database_path('migrations/'.date('Y_m_d_His').'_create_'.$name.'_table.php');

        $migrationContent = <<<EOT
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                /**
                 * Run the migrations.
                 */
                public function up()
                {
                    Schema::create('{$name}', function (Blueprint \$table) {
                        \$table->id();
                        // Add your columns here

                        \$table->softDeletes();
                        \$table->timestamps();
                    });
                }

                /**
                 * Reverse the migrations.
                 */
                public function down()
                {
                    Schema::dropIfExists('{$name}');
                }
            };
            EOT;

        file_put_contents($migration, $migrationContent);
    }

    protected function createResource()
    {
        $name = $this->argument('name');
        $this->call('make:resource', ['name' => "{$name}Resource"]);
        $resource = app_path("Http/Resources/{$name}Resource.php");

        $resourceContent = <<<EOT
            <?php

            namespace App\Http\Resources;

            use Illuminate\Http\Resources\Json\JsonResource;
            use App\Helpers\HashidsHelper; // Menggunakan HashidsHelper yang sudah ada

            class {$name}Resource extends JsonResource
            {
                /**
                 * Transform the resource into an array.
                 *
                 * @param  \Illuminate\Http\Request  \$request
                 * @return array<string, mixed>
                 */
                public function toArray(\$request)
                {
                    return [
                        'id' => HashidsHelper::encodeId(\$this->id),
                        // Add your columns here
                        'created_at' => \$this->created_at,
                        'updated_at' => \$this->updated_at,
                        'deleted_at' => \$this->deleted_at,
                    ];
                }
            }
            EOT;

        file_put_contents($resource, $resourceContent);
    }

    protected function createFactory()
    {
        $name = $this->argument('name');
        $this->call('make:factory', ['name' => "{$name}Factory"]);
        $factory = database_path("factories/{$name}Factory.php");

        $factoryContent = <<<EOT
            <?php

            namespace Database\Factories;

            use Illuminate\Database\Eloquent\Factories\Factory;
            use Illuminate\Support\Str;

            class {$name}Factory extends Factory
            {
                /**
                 * Define the model's default state.
                 *
                 * @return array<string, mixed>
                 */
                public function definition(): array
                {
                    return [
                        // Define your default state here
                    ];
                }
            }
            EOT;

        file_put_contents($factory, $factoryContent);
    }

    protected function modifyRepository()
    {
        $name = $this->argument('name');
        $interfacePath = app_path("Interfaces/{$name}RepositoryInterface.php");
        $repositoryPath = app_path("Repositories/{$name}Repository.php");

        $interfaceContent = $this->generateInterfaceContent($name);

        $repositoryContent = $this->generateRepositoryContent($name);

        file_put_contents($interfacePath, $interfaceContent);
        file_put_contents($repositoryPath, $repositoryContent);

        $this->updateRepositoryServiceProvider($name);
    }

    protected function generateInterfaceContent($name)
    {
        $interfaceContent = <<<'EOT'
    <?php

    namespace App\Interfaces;

    interface __namePascalCase__RepositoryInterface
    {
        public function getAll(
            ?string $search,
            ?int $limit,
            bool $execute,
        );

        public function getAllPaginated(
            ?string $search,
            int $rowsPerPage
        );

        public function getById(int $id, bool $withTrashed);

        public function create(array $data);

        public function update(array $data, int $id);

        public function delete(int $id);
    }
    EOT;

        $interfaceContent = str_replace('__namePascalCase__', $name, $interfaceContent);
        $interfaceContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCase__', Str::camel($name), $interfaceContent);
        $interfaceContent = str_replace('__nameSnakeCase__', Str::snake($name), $interfaceContent);
        $interfaceContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $interfaceContent);
        $interfaceContent = str_replace('__nameKebabCase__', Str::kebab($name), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $interfaceContent);

        return $interfaceContent;
    }

    protected function generateRepositoryContent($name)
    {
        $repositoryContent = <<<'EOT'
    <?php

    namespace App\Repositories;

    use App\Interfaces\__namePascalCase__RepositoryInterface;
    use App\Models\__namePascalCase__;
    use Exception;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;

    class __namePascalCase__Repository implements __namePascalCase__RepositoryInterface
    {
        public function getAll(
            ?string $search,
            ?int $limit,
            bool $execute
        ) {
            $query = __namePascalCase__::withTrashed()->where(function ($query) use ($search) {
                $query->withoutTrashed();

                if ($search) {
                    $query->search($search);
                }
            });

            $query->orderBy('name', 'asc');

            if ($limit) {
                $query->take($limit);
            }

            if ($execute) {
                return $query->get();
            } else {
                return $query;
            }
        }

        public function getAllPaginated(?string $search, int $rowsPerPage)
        {
            $query = $this->getAll(
                search: $search,
                limit: null,
                execute: false
            );

            return $query->paginate($rowsPerPage);
        }

        public function getById(int $id, bool $withTrashed)
        {
            $query = __namePascalCase__::where('id', '=', $id);

            if ($withTrashed) {
                $query = $query->withTrashed();
            }

            return $query->first();
        }

        public function create(array $data)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = new __namePascalCase__();
                // Add your columns here
                $__nameCamelCase__->save();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

        public function update(array $data, int $id)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = __namePascalCase__::find($id);
                // Add your columns here
                $__nameCamelCase__->save();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

        public function delete(int $id)
        {
            DB::beginTransaction();

            try {
                $__nameCamelCase__ = __namePascalCase__::find($id);
                $__nameCamelCase__->delete();

                DB::commit();

                return $__nameCamelCase__;
            } catch (\Exception $e) {
                DB::rollBack();

                throw new Exception($e->getMessage());
            }
        }

        private function saveImage($image)
        {
            if ($image) {
                return $image->store('assets/__nameKebabCase__/images', 'public');
            } else {
                return null;
            }
        }

        private function updateImage($oldImage, $newImage)
        {
            if ($newImage) {
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }

                return $newImage->store('assets/__nameKebabCase__/images', 'public');
            } else {
                return $oldImage;
            }
        }
    }
    EOT;

        $repositoryContent = str_replace('__namePascalCase__', $name, $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCase__', Str::camel($name), $repositoryContent);
        $repositoryContent = str_replace('__nameSnakeCase__', Str::snake($name), $repositoryContent);
        $repositoryContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $repositoryContent);
        $repositoryContent = str_replace('__nameKebabCase__', Str::kebab($name), $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $repositoryContent);

        return $repositoryContent;
    }

    protected function updateRepositoryServiceProvider($name)
    {
        $repositoryServiceProvider = app_path('Providers/RepositoryServiceProvider.php');
        $repositoryServiceProviderContent = file_get_contents($repositoryServiceProvider);

        $replacement = "\$this->app->bind(\App\Interfaces\\{$name}RepositoryInterface::class, \App\Repositories\\{$name}Repository::class);\n    }\n";

        $pattern = '/public function register\(\)\s*{([^}]*)}/s';
        $repositoryServiceProviderContent = preg_replace($pattern, "public function register() {\n$1$replacement", $repositoryServiceProviderContent, 1);

        file_put_contents($repositoryServiceProvider, $repositoryServiceProviderContent);
    }

    protected function addRoutes()
    {
        $name = $this->argument('name');

        $name = Str::kebab($name);
        $routes = base_path('routes/api.php');

        $routeContent = "\nRoute::Apiresource('{$name}', App\Http\Controllers\Web\Api\\{$this->argument('name')}Controller::class);";

        file_put_contents($routes, $routeContent, FILE_APPEND);
    }
}