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
                            <a class="btn btn-success float-right cursor-pointer" href="#"> Add New</a>
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
                                <tbody>
                                    @foreach($users as $item)
                                        <tr class="user-{{ $item->id }}">
                                            <td>{{ $item->first_name. ' ' .$item->last_name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->type == 2 ? 'Dealer' : 'Consumer' }}</td>
                                            <td>
                                                <span class="p-1 {{ $item->status == 2 ? 'text-warning' : 'text-success' }}">{{ $item->status == 2 ? 'Pending' : 'Active' }}</span>
                                            </td>
                                            <td style="vertical-align: middle;text-align: center;">
                                                @if($item->status == 2)
                                                    <a href="#" class="btn btn-raised btn-xs btn-success" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="{{ $item->id }}" data-status="1" title="Active"><i class="fas fa-check"></i></a>
                                                @else
                                                    <a href="#" class="btn btn-raised btn-xs btn-warning" data-toggle="modal" id="changeStatus" data-target="#statusModal" data-id="{{ $item->id }}" data-status="1" title="Inactive"><i class="fas fa-ban"></i></a>
                                                @endif
                                                <a href="#" class="btn btn-raised btn-xs btn-primary" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="btn btn-raised btn-xs btn-info" title="Edit"><i class="fas fa-edit"></i></a>
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

     <!-- Delete Class Modal -->
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

     <!-- Delete Class Modal -->
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