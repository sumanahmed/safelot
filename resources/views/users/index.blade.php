@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Users') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mt-2 tx-spacing--1 float-left">All Users</h4>
                            <a class="btn btn-success float-right cursor-pointer" data-toggle="modal" data-target="#createModal" href="#"> Add New</a>
                        </div>

                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="vertical-align: middle;text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="vertical-align: middle;text-align: center;">Action</th>
                                    </tr>
                                </tfoot>
                                <tbody id="allUser">
                                    @foreach($users as $item)
                                        <tr class="user-{{ $item->id }}">
                                            <td>{{ $item->first_name. ' ' .$item->last_name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->type == 1 ? 'Admin User' : ($item->type == 2 ? 'Dealer' : 'Consumer') }}</td>
                                            <td>
                                                <span class="p-1 {{ $item->status == 2 ? 'text-warning' : 'text-success' }}">{{ $item->status == 2 ? 'Pending' : 'Active' }}</span>
                                            </td>
                                            <td style="vertical-align: middle;text-align: center;">
                                                @if($item->status == 2)
                                                    <a href="#" class="btn btn-raised btn-xs btn-success" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="{{ $item->id }}" data-status="1" title="Active"><i class="fas fa-check"></i></a>
                                                @else
                                                    <a href="#" class="btn btn-raised btn-xs btn-warning" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="{{ $item->id }}" data-status="1" title="Inactive"><i class="fas fa-ban"></i></a>
                                                @endif
                                                <a href="#" class="btn btn-raised btn-xs btn-primary" title="View"  data-id="{{ $item->id }}" data-first_name="{{ $item->first_name }}" data-last_name="{{ $item->last_name }}" data-email="{{ $item->email }}"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="btn btn-raised btn-xs btn-info edit" title="Edit" data-id="{{ $item->id }}" data-first_name="{{ $item->first_name }}" data-last_name="{{ $item->last_name }}" data-email="{{ $item->email }}"><i class="fas fa-edit"></i></a>
                                                <a href="#" class="btn btn-raised btn-xs btn-danger" data-toggle="modal" id="deleteRow" data-target="#deleteModal" data-id="{{ $item->id }}" data-status="0" title="Delete"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer clearfix">
                            {{ $users->links() }}
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
    
    <!-- Create Modal -->
    <div id="createModal" class="modal fade bd-example-modal-xl mt-3" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content tx-14">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Add New</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mg-b-0" style="padding: 2px 15px !important;">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="first_name">First Name <span class="text-danger text-bold" title="Required Field">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control"placeholder="Enter First Name" required>
                                <span class="text-danger firstNameError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="last_name">Last Name </label>
                                <input type="text" name="last_name" id="last_name" class="form-control"placeholder="Enter Last Name">
                                <span class="text-danger lastNameError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="email">Email<span class="text-danger text-bold" title="Required Field">*</span></label>
                                <input type="text" name="email" id="email" class="form-control"placeholder="Enter Email Address" required>
                                <span class="text-danger emailError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="password">Password<span class="text-danger text-bold" title="Required Field">*</span></label>
                                <input type="text" name="password" id="password" class="form-control"placeholder="Enter Password" required>
                                <span class="text-danger passwordError"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="button" class="btn btn-success tx-13" id="save">Save</button>
                            <button type="button" class="btn btn-danger tx-13" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade bd-example-modal-xl mt-3" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content tx-14">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mg-b-0" style="padding: 2px 15px !important;">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="first_name">First Name <span class="text-danger text-bold" title="Required Field">*</span></label>
                                <input type="text" name="first_name" id="edit_first_name" class="form-control"placeholder="Enter First Name" required>
                                <input type="hidden" id="edit_id" />
                                <span class="text-danger editFirstNameError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="last_name">Last Name </label>
                                <input type="text" name="last_name" id="edit_last_name" class="form-control"placeholder="Enter Last Name">
                                <span class="text-danger editLastNameError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="email">Email<span class="text-danger text-bold" title="Required Field">*</span></label>
                                <input type="text" name="email" id="edit_email" class="form-control"placeholder="Enter Email Address" required>
                                <span class="text-danger editEmailError"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="password">Password</label>
                                <input type="text" name="password" id="edit_password" class="form-control"placeholder="Enter Password" required>
                                <span class="text-danger editPasswordError"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <button type="button" class="btn btn-success tx-13" id="update">Update</button>
                            <button type="button" class="btn btn-danger tx-13" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Status Change Modal -->
     <div id="statusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-center">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure ?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status_row_id"/>
                    <button type="button" class="btn btn-danger btn-raised mr-2" id="statusUpdate">Yes</button>
                    <button type="button" class="btn btn-warning btn-raised" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Delete Modal -->
     <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure ?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="delete_row_id"/>
                    <button type="button" class="btn btn-danger btn-raised mr-2" id="destroy"> Delete</button>
                    <button type="button" class="btn btn-warning btn-raised" data-dismiss="modal" aria-label="Close"> Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/user.js') }}" defer></script>
@endsection('scripts')