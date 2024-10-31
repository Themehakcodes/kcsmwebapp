@extends('admin.layouts.app')
@section('admincontent')
<!-- Content wrapper -->

<div class="content-wrapper">

<!-- Content -->

  <div class="container-xxl flex-grow-1 container-p-y">
 
  
    
<h4 class="mb-1">Roles List</h4>
<p class="mb-6">A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what user needs.</p>


<!-- Role cards -->
<div class="row g-6" id="roleCardsContainer">
    @foreach ($roles as $index => $role)
        <div class="col-xl-4 col-lg-6 col-md-6 role-card {{ $index >= 5 ? 'd-none' : '' }}">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <p class="mb-0">Total {{ $role->users->count() }} users</p>
                        <ul class="mb-0 list-unstyled d-flex align-items-center avatar-group">
                            @foreach ($role->users->take(3) as $user)
                                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $user->name }}" class="avatar pull-up">
                                    <img class="rounded-circle" src="admin/assets/img/avatars/1.png" alt="{{ $user->name }}">
                                </li>
                            @endforeach
                            @if ($role->users->count() > 3)
                                <li class="avatar">
                                    <span class="avatar-initial rounded-circle pull-up text-body" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $role->users->count() - 3 }} more">+{{ $role->users->count() - 3 }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="role-heading">
                            <h5 class="mb-1">{{ $role->title }}</h5>
                            @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Roles_Management_Write')))
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#editRoleModal" class="role-edit-modal" data-role-id="{{ $role->id }}" data-role-name="{{ $role->title }}" data-permissions="{{ json_encode($role->permissions->pluck('id')->toArray()) }}">
                                <p class="mb-0">Edit Role</p>
                            </a>
                            @endif
                        </div>
                        <a href="javascript:void(0);" class="text-secondary"><i class="ri-file-copy-line ri-22px"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Roles_Management_Create')))
    <!-- Add New Role Card (Always Visible) -->
    <div class="col-xl-4 col-lg-6 col-md-6 role-card">
        <div class="card h-100">
            <div class="row h-100">
                <div class="col-5">
                    <div class="d-flex align-items-end h-100 justify-content-center">
                        <img src="admin/assets/img/illustrations/illustration-3.png" class="img-fluid" alt="Image" width="80">
                    </div>
                </div>
                <div class="col-7">
                    <div class="text-center card-body text-sm-end ps-sm-0">
                        <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="mb-4 btn btn-sm btn-primary text-nowrap add-new-role">Add New Role</button>
                        <p class="mb-0">Add role, if it does not exist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Show All Roles Button (Right-Al`igned Initially) -->
<div class="mt-4 d-flex justify-content-end" id="showAllRolesBtnContainer">
    <button id="showAllRolesBtn" class="btn btn-outline-primary">Show all Roles</button>
</div>

<!-- Collapse Button (Centered Initially Hidden) -->
<div class="mt-4 text-center d-none" id="collapseRolesBtnContainer">
    <button id="collapseRolesBtn" class="btn btn-outline-secondary">Collapse Roles</button>
</div>




<div class="col-12">
<h4 class="mt-6 mb-1">Total users with their roles</h4>
<p class="mb-4">Find all of your company’s administrator accounts and their associate roles.</p>
</div>
<div class="col-12">
<!-- Role Table -->
<div class="card">
    <div class="card-datatable table-responsive datatable-roles">
        <table class="table datatables-users">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Avatar</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Roles_Management_Write')))
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Adjusted index to start from 1 -->
                    <td>
                        <img src="{{ asset('admin/assets/img/avatars/' . ($user->avatar ?? '1.png')) }}" alt="Avatar" class="rounded-circle" width="40">
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->roles->isEmpty())
                            No Roles
                        @else
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->title }}</span>
                            @endforeach
                        @endif
                    </td>

                    <td>
                    <span class="badge 
    {{ 
        $user->profile_status == 'activated' ? 'badge bg-label-success rounded-pill' : 
        ($user->profile_status == 'deactivated' ? 'badge bg-label-danger rounded-pill' : 
        ($user->profile_status == 'hold' ? 'badge bg-label-warning rounded-pill' : '')) 
    }} rounded-pill">
    {{ ucfirst($user->profile_status) }}
</span>
                    </td>
                    @if(auth()->check() && (auth()->user()->is_superadmin || auth()->user()->hasPermission('Roles_Management_Write')))
                    <td>

                        <button type="button" class="btn btn-primary edit-user-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editUser" 
                            data-id="{{ $user->id }}" 
                            data-name="{{ $user->name }}" 
                            data-status="{{ $user->profile_status }}" 
                            data-roles="{{ json_encode($user->roles->pluck('id')) }}"> 
                            Edit
                        </button>

                    </td>

                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!--/ Role Table --> 


</div>
</div>
<!--/ Role cards -->

<!--  Modal -->


<!-- Add Role Modal -->

                <!--/ Edit role form -->



                




            </div>
        </div>
    </div>
</div>



<!-- Edit User Modal -->

<!--/ Edit User Modal -->


<!-- /  Modal -->
  </div>
  <!-- / Content -->

  
<!-- Footer --> 
 
<!-- / Footer -->

  
  <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
</div>

<!-- JavaScript to Handle Modal Opening and Closing -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for session messages
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


  

    // Show/Hide Role Cards Logic
    const showAllBtn = document.getElementById('showAllRolesBtn');
    const collapseBtnContainer = document.getElementById('collapseRolesBtnContainer');
    const roleCards = document.querySelectorAll('.role-card.d-none');
    const showAllBtnContainer = document.getElementById('showAllRolesBtnContainer');

    if (showAllBtn) {
        showAllBtn.addEventListener('click', function () {
            // Show hidden cards        
            roleCards.forEach(card => {
                card.classList.remove('d-none');
            });

            // Hide the "Show All" button and show the "Collapse" button
            showAllBtnContainer.classList.add('d-none');
            collapseBtnContainer.classList.remove('d-none');
            collapseBtnContainer.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll
        });
    }

    const collapseRolesBtn = document.getElementById('collapseRolesBtn');
    if (collapseRolesBtn) {
        collapseRolesBtn.addEventListener('click', function () {
            // Hide the extra role cards
            roleCards.forEach(card => {
                card.classList.add('d-none');
            });

            // Show the "Show All" button and hide the "Collapse" button
            collapseBtnContainer.classList.add('d-none');
            showAllBtnContainer.classList.remove('d-none');
            showAllBtnContainer.scrollIntoView({ behavior: 'smooth' });
        });
    }


  

    // Edit Role Modal Logic
    document.querySelectorAll('.role-edit-modal').forEach(button => {
        button.addEventListener('click', function() {
            const roleId = this.getAttribute('data-role-id');
            const roleName = this.getAttribute('data-role-name');
            const permissions = JSON.parse(this.getAttribute('data-permissions'));

            document.getElementById('editModalRoleName').value = roleName;

            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false; // Reset checkboxes
            });

            // Check permissions
            permissions.forEach(permissionId => {
                const checkbox = document.getElementById('edit_' + permissionId);
                if (checkbox) {
                    checkbox.checked = true; // Check the relevant checkboxes
                }
            });

            // Update form action with the role ID
            document.getElementById('editRoleForm').action = document.getElementById('editRoleForm').action.replace(':role_id', roleId);
        });
    });

    // Reset Edit Role Modal on close
    const editRoleModal = document.getElementById('editRoleModal'); 
if (editRoleModal) {
    editRoleModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('editRoleForm').reset();

        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = false; // Reset checkboxes on modal close
        });
        
        // Clear the role name input field explicitly
        document.getElementById('editModalRoleName').value = '';
    });
}

    // Edit User Modal Logic
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id'); 
            const userName = this.getAttribute('data-name');
            const userStatus = this.getAttribute('data-status'); 
            const userRoles = JSON.parse(this.getAttribute('data-roles')); 

            document.getElementById('modalEditUserName').value = userName; 
            document.getElementById('editUserId').value = userId; 

            // Check user roles
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.checked = userRoles.includes(parseInt(checkbox.value));
            });

            document.getElementById('userStatusSelect').value = userStatus; 
        });
    });

    // Reset Edit User Modal on close
    const editUserModal = document.getElementById('editUser'); 
    if (editUserModal) {
        editUserModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('editUserForm').reset();

            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.checked = false; // Reset checkboxes on modal close
            });

            document.getElementById('userStatusSelect').value = 'activated'; // Reset status to default
        });
    }
});
</script>


@endsection