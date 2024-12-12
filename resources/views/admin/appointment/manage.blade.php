@extends('admin.adminBase')

@section('title')
    Manage Services |
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Appointments</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Appointments</li>
                        <li class="breadcrumb-item active">Manage</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row ">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Date Range
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dateRangeDropdown">
                                                <a class="dropdown-item" href="#" data-range="today">Today</a>
                                                <a class="dropdown-item" href="#" data-range="yest_week">Last Week</a>
                                                <a class="dropdown-item" href="#"
                                                    data-range="lassterday">Yesterday</a>
                                                <a class="dropdown-item" href="#" data-range="lat_month">Last
                                                    Month</a>
                                                <a class="dropdown-item" href="#" data-range="this_year">This Year</a>
                                                <a class="dropdown-item" href="#" data-range="custom">Custom</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <!-- Date Range Picker -->
                                        <div id="customDateRangePicker" style="display: none;">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">From</span>
                                                </div>
                                                <input type="text" id="startDate" name="startDate" class="form-control">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">To</span>
                                                </div>
                                                <input type="text" id="endDate" name="endDate" class="form-control">
                                                <div class="input-group-append">
                                                    <button type="button" id="applyCustomDateRange"
                                                        class="btn btn-primary">Apply</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="table_search" id="searchField"
                                            class="form-control float-right" placeholder="Search">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default" id="searchButton">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Preferred Date</th>
                                        <th>Preferred Time</th>
                                        <th>Requirements</th>
                                        <th>Contact</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- Table rows will be dynamically added here -->
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>

        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function() {


            // Initialize date picker for custom date range
            $('#startDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $('#endDate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            $('.dropdown-item[data-range="custom"]').click(function() {
                $('#customDateRangePicker').show();
            });

            // Hide custom date range picker when other date ranges are clicked
            $('.dropdown-item').not('[data-range="custom"]').click(function() {
                $('#customDateRangePicker').hide();
            });
            // Apply custom date range filter when 'Apply' button is clicked
            $('#applyCustomDateRange').click(function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                // Call the filterAppointments function with custom date range
                filterAppointments('custom', startDate, endDate);

                // Hide the custom date range picker
                $('#customDateRangePicker').hide();
            });
            // AJAX call to fetch data
            function filterAppointments(dateRange = "today", startDate = null, endDate = null, search = null) {

                if (dateRange === 'today') {
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = today.getFullYear();

                    startDate = yyyy + '-' + mm + '-' + dd;
                    endDate = yyyy + '-' + mm + '-' + dd;
                }
                // console.log(complainNo) //debug
                var data = {
                    dateRange: dateRange,
                    startDate: startDate,
                    endDate: endDate,
                    search: search,
                };

                // console.log(complainNo) //debug


                $.ajax({
                    url: "{{ route('appointment.index') }}",
                    type: 'GET',
                    data: data,

                    success: function(response) {
                        if (response && response.length > 0) {
                            var tableRows = '';
                            response.forEach(function(row) {
                                var requirements = JSON.parse(row.requirements);
                                var requirementsHtml = '';

                                // console.log(complainNo) //debug

                                $.each(requirements, function(i, requirement) {
                                    requirementsHtml +=
                                        `<span class="badge bg-success">${requirement}</span> `;
                                });

                                tableRows +=
                                    `<tr> 
                                        <td>${row.complain_no}</td>
                                        <td>${row.fullname}</td>
                                        <td>${row.preferred_date}</td>
                                        <td>${row.preferred_time}</td>
                                        <td>${requirementsHtml}</td>
                                        <td>${row.mobileno}</td>
                                        <td> <a href='/admin/appointment/view/${row.id}'class='btn btn-warning'>View</a> </td>
                                    </tr>`;
                            });
                            $('#tableBody').html(tableRows);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Example usage for filtering appointments
            $('.dropdown-item[data-range]').click(function() {
                var dateRange = $(this).data('range');
                if (dateRange !== 'custom') {
                    filterAppointments(dateRange);
                }
            });

            // Search functionality
            $('#searchField').keyup(function() {
                var search = $(this).val();
                filterAppointments('custom', null, null, search);
            });

            filterAppointments();

        });
    </script>
@endsection
