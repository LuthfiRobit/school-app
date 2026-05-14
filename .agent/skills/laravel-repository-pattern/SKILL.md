---
Name: Laravel 12 Advanced Repository Pattern
Description: Menerapkan Repository Pattern lengkap dengan Base Repository, Interface, dan Auto-Binding untuk Laravel 12.
---

# Laravel 12 Advanced Repository Pattern

## Context
Gunakan skill ini saat membuat Model atau fitur CRUD. Kamu wajib menggunakan abstraksi Base Repository untuk menghindari pengulangan kode (DRY).

## 1. Base Structure (Wajib Dibuat Pertama Kali)

### Base Interface: `app/Repositories/Interfaces/EloquentRepositoryInterface.php`
```php
<?php
namespace App\Repositories\Interfaces;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface {
    public function all(array \(columns = ['*'], array\)relations = []): Collection;
    public function find(int \$id, array \(columns = ['*'], array\)relations = []): ?Model;
    public function create(array \$data): Model;
    public function update(int \(id, array\)data): bool;
    public function delete(int \$id): bool;
}
```

### Base Repository: `app/Repositories/BaseRepository.php`
```php
<?php
namespace App\Repositories;
use App\Repositories\Interfaces\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements EloquentRepositoryInterface {
    public function __construct(protected Model \$model) {}

    public function all(array \(columns = ['*'], array\)relations = []): Collection {
        return \$this->model->with(\(relations)->get(\)columns);
    }

    public function find(int \$id, array \(columns = ['*'], array\)relations = []): ?Model {
        return \(this->model->with(\)relations)->findOrFail(\(id,\)columns);
    }

    public function create(array \$data): Model {
        return \(this->model->create(\)data);
    }

    public function update(int \(id, array\)data): bool {
        return \$this->find(\(id)->update(\)data);
    }

    public function delete(int \$id): bool {
        return \(this->find(\)id)->delete();
    }
}
```

## 2. Model Specific Workflow

Setiap kali membuat repository baru (misal: `Post`):
1. **Interface**: Buat `PostRepositoryInterface` yang meng-extend `EloquentRepositoryInterface`.
2. **Repository**: Buat `PostRepository` yang meng-extend `BaseRepository` dan meng-implementasikan `PostRepositoryInterface`.
3. **Binding**: Tambahkan di `AppServiceProvider`.

## Strict Rules
- **Type Safety**: Gunakan return types dan parameter types (PHP 8.2+).
- **Controller**: Dependency Injection hanya boleh memanggil Interface, bukan class concrete.
- **Eloquent**: Logic query database hanya boleh ada di dalam file Repository.
