@extends('admin.adminBase')


@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $count_appointment }}</h3>

                            <p>Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('admin.appointment.manage') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $count_user }}</h3>

                            <p>New Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">

                        <div class="inner">
                            <h3>{{ $count_user }}</h3>
                            <p>Total User</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{ route('admin.users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>65</h3>

                            <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="{{ route('admin.unique.visitors') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <!-- /.card -->

                    <div class="card">
                        <div class="card-header border-0 bg-black">
                            <h3 class="card-title">Unique visitors</h3>
                            <div class="card-tools">
                                <a href="#" class="btn btn-tool btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="#" class="btn btn-tool btn-sm">
                                    <i class="fas fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm text-sm table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>Complain No</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Requirement</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->


    </div>
@endsection


@section('js')
    <script>
        $.ajax({
            url: '{{ route('appointment.index') }}',
            type: 'GET',
            success: function(response) {
                // Assuming your table body has an id of 'tableBody'
                var tableBody = $('#tableBody');

                // Clear existing table data
                tableBody.empty();

                // Iterate over the JSON data
                $.each(response, function(index, row) {
                    // checking here if the status is 'process':
                    if (row.status != 'done' && row.status != "reject") {
                        // Parse the requirements JSON array
                        var requirements = JSON.parse(row.requirements);
                        var requirementsHtml = '';

                        // Create a badge for each requirement
                        $.each(requirements, function(i, requirement) {
                            requirementsHtml +=
                                `<span class="badge bg-success">${requirement}</span> `;
                        });

                        // Append the table row with badge to the table body
                        var tableRow = `
                <tr>
                    <td>${row.complain_no}</td>
                    <td>${row.fullname}</td>
                    <td>${row.preferred_date} (${row.preferred_time})</td>
                    <td>${requirementsHtml}</td>
                    <td>${row.mobileno}</td>
                    <td>${row.address} ${row.city}</td>
                    <td><a href='/admin/appointment/view/${row.id}' class='btn btn-warning'>View</a></td>
                </tr>`;


                        tableBody.append(tableRow);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    </script>
@endsection
