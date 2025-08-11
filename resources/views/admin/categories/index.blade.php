@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h3>{{ __('categories.management') }}</h3>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">{{ __('categories.add') }}</a>
    </div>

    
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('categories.error occurred') }}</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="categories-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>{{ __('categories.code') }}</th> {{-- Tambahan --}}
                        <th>{{ __('categories.category name') }}</th>
                        <th>{{ __('categories.description') }}</th>
                        <th>{{ __('categories.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->code }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">{{ __('categories.edit') }}</a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('categories.delete confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">{{ __('categories.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tombol Ekspor & Impor --}}
    <a href="{{ route('categories.export') }}" class="btn btn-success mt-3">{{ __('categories.export') }}</a>
    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#importModal">{{ __('categories.import') }}</button>
    <a href="{{ route('categories.downloadTemplate') }}" class="btn btn-link mt-3">{{ __('categories.download template') }}</a>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">{{ __('categories.import') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('categories.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('categories.import') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#categories-table').DataTable({
            language: {
                url: "{{ asset(App::getLocale() === 'id' ? 'assets/indonesia.json' : 'assets/english.json') }}"
            }
        });
    });
</script>
@endpush
