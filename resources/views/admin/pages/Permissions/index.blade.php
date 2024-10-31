@extends('admin.layouts.app')
@section('admincontent')
    <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                            Overview
                        </div>
                        <h2 class="page-title">
                            Permissions Management
                        </h2>
                    </div>
                    <!-- Page title actions -->
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Permissions_Management_Create')))
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                Add Permission
                            </button>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
    
            <div class="container-xxl flex-grow-1 container-p-y">
        
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
        
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
        
                <div class="shadow-lg card">
                    <div class="p-4 card-body">
                        <div class="table-responsive">
                            <table class="table datatables-permissions">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Assigned To</th>
                                        <th>Created Date</th>
                                        @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Permissions_Management_Write')))
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $index => $permission)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td></td>
                                            <td>{{ $permission->title }}</td>
                                            <td>
                                                @foreach ($permission->roles as $role)
                                                    <span class="badge bg-primary">{{ $role->title }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $permission->created_at->format('d-m-Y') }}</td>
        
                                            @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Permissions_Management_Write')))
                                                <td>
                                                    <div class="flex flex-col">
                                                        <!-- Edit Permission Modal Trigger -->
                                                        <button type="button" class="btn btn-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editPermissionModal" 
                                                            data-id="{{ $permission->id }}" 
                                                            data-title="{{ $permission->title }}">
                                                            Edit
                                                        </button>
        
                                                        <!-- Delete Permission Modal Trigger -->
                                                        <button type="button" class="btn btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deletePermissionModal" 
                                                            data-id="{{ $permission->id }}">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        
                <!-- Add Permission Modal -->
                <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="p-4 modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="mb-6 text-center">
                                    <h4 class="mb-2">Add New Permission</h4>
                                    <p>Permissions you may use and assign to your users.</p>
                                </div>
                                <form method="POST" action="{{ action([\App\Http\Controllers\Admin\PermissionsController::class, 'store']) }}" class="row">
                                    @csrf
                                    <div class="mb-4 col-12">
                                        <div class="form-floating">
                                            <input type="text" id="modalPermissionName" name="modalPermissionName" class="form-control" placeholder="Permission Name" required autofocus />
                                            <label for="modalPermissionName">Permission Name</label>
                                        </div>
                                    </div>
                                    <div class="mb-2 col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="corePermission" id="corePermission" />
                                            <label class="form-check-label" for="corePermission">Set as core permission</label>
                                        </div>
                                    </div>
                                    <div class="text-center col-12">
                                        <button type="submit" class="btn btn-primary me-3">Create Permission</button>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Discard</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Edit Permission Modal -->
                <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="p-4 modal-body">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div class="mb-6 text-center">
                                    <h4 class="mb-2">Edit Permission</h4>
                                    <p>Edit permission as per your requirements.</p>
                                </div>
                                <div class="alert alert-warning d-flex align-items-start" role="alert">
                                    <span class="alert-icon me-4 rounded-2"><i class="ri-alert-line ri-22px"></i></span>
                                    <span>
                                        <h5 class="mb-1 alert-heading">Warning</h5>
                                        <p class="mb-0">By editing the permission name, you might break the system permissions functionality. Please ensure you're absolutely certain before proceeding.</p>
                                    </span>
                                </div>
                                <form method="POST" action="" id="editPermissionForm" class="pt-2 row gx-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="editPermissionId" name="id" />
                                    <div class="mb-4 col-sm-9">
                                        <input type="text" id="editPermissionName" name="editPermissionName" class="form-control" placeholder="Permission Name" required />
                                    </div>
                                    <div class="mb-4 col-sm-3">
                                        <button type="submit" class="mt-1 btn btn-primary">Update</button>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="editCorePermission" id="editCorePermission" />
                                            <label class="form-check-label" for="editCorePermission">Set as core permission</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deletePermissionModalLabel">Delete Permission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="text-center modal-body">
                                <p>Are you sure you want to delete this permission? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form id="deletePermissionForm" method="POST" action="">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">New report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="example-text-input"
                                placeholder="Your report name">
                        </div>
                        <label class="form-label">Report type</label>
                        <div class="mb-3 form-selectgroup-boxes row">
                            <div class="col-lg-6">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="report-type" value="1" class="form-selectgroup-input"
                                        checked>
                                    <span class="p-3 form-selectgroup-label d-flex align-items-center">
                                        <span class="me-3">
                                            <span class="form-selectgroup-check"></span>
                                        </span>
                                        <span class="form-selectgroup-label-content">
                                            <span class="mb-1 form-selectgroup-title strong">Simple</span>
                                            <span class="d-block text-secondary">Provide only basic data needed for the
                                                report</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="report-type" value="1" class="form-selectgroup-input">
                                    <span class="p-3 form-selectgroup-label d-flex align-items-center">
                                        <span class="me-3">
                                            <span class="form-selectgroup-check"></span>
                                        </span>
                                        <span class="form-selectgroup-label-content">
                                            <span class="mb-1 form-selectgroup-title strong">Advanced</span>
                                            <span class="d-block text-secondary">Insert charts and additional advanced
                                                analyses to be inserted in the report</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Report url</label>
                                    <div class="input-group input-group-flat">
                                        <span class="input-group-text">
                                            https://tabler.io/reports/
                                        </span>
                                        <input type="text" class="form-control ps-0" value="report-01"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Visibility</label>
                                    <select class="form-select">
                                        <option value="1" selected>Private</option>
                                        <option value="2">Public</option>
                                        <option value="3">Hidden</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Client name</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Reporting period</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Additional information</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                            Cancel
                        </a>
                        <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Create new report
                        </a>
                    </div>
                </div>
            </div>
        </div>

         <!-- JavaScript for handling Edit and Delete Modals -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Edit Modal Script
            document.querySelectorAll('.edit-permission-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    document.getElementById('editPermissionId').value = id;
                    document.getElementById('editPermissionName').value = title;

                    // Set form action for the edit
                    document.getElementById('editPermissionForm').action = "{{ action([\App\Http\Controllers\Admin\PermissionsController::class, 'update'], ':id') }}".replace(':id', id);
                });
            });

            // Delete Modal Script
            document.querySelectorAll('.btn-danger[data-bs-target="#deletePermissionModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('deletePermissionForm');

                    // Update the form action with the controller path and permission ID
                    form.action = "{{ action([\App\Http\Controllers\Admin\PermissionsController::class, 'destroy'], ':id') }}".replace(':id', id);
                });
            });
        });
    </script>
    @endsection
