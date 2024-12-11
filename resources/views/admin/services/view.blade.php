@extends('admin.adminBase')

@section('title')
    Manage Services |
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 viewtitle">View Services</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Services</li>
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
                            <h3 class="card-title viewtitle">All Services</h3>

                            <div class="card-tools">
                                <a href="{{ route('admin.service.manage') }}" class="btn btn-sm btn-primary">Go Back</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <tbody id="tableBody">
                                </tbody>
                            </table>

                        </div>
                        <!-- /.card-body -->
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Services Fees</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h2 class="card-title">Insert Here</h2>
                                        </div>
                                        <div class="card-body">
                                            <form id="createServiceFeeForm">
                                                @csrf
                                                <div class="mb-3">

                                                    <label for="service_id">Service ID:</label>
                                                    <input type="text" class="form-control" id="service_id"
                                                        name="service_id" required>
                                                </div>
                                                <div class="mb-3">

                                                    <label for="service_fees_name">Service Fees Name:</label>
                                                    <input type="text" class="form-control" id="service_fees_name"
                                                        name="service_fees_name" required>
                                                </div>
                                                <div class="mb-3">

                                                    <label for="service_fees">Service Fees:</label>
                                                    <input type="text" class="form-control" id="service_fees"
                                                        name="service_fees" required>
                                                </div>
                                                <div class="mb-3">

                                                    <label for="parent_id">Parent ID:</label>
                                                    <select type="text" class="form-control" id="parent_id"
                                                        name="parent_id">

                                                    </select>
                                                </div>

                                                <button type="submit">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="accordion" id="accordionExample">

                                    </div>
                                </div>
                            </div>
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
        <script>
            $(document).ready(function() {
                let service_id = @json($service->id);

                // service fee calling here
                const CallingServiceFees = () => {
                    $.ajax({
                        url: '{{ route('servicefee.index') }}',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            "service_id": service_id
                        },
                        success: function(response) {
                            $('#accordionExample').empty();
                            response.forEach(function(item) {

                                var accordionItem = `
                                <div class="card">
                                    <div class="card-header" id="heading${item.id}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link text-capitalize btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse${item.id}" aria-expanded="true" aria-controls="collapse${item.id}">
                                                ${item.service_fees_name}
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapse${item.id}" class="collapse" aria-labelledby="heading${item.id}" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <ul class="list-group">
                                            `;
                                item.sub_fees.forEach(function(subFee) {
                                    accordionItem += `
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>${subFee.service_fees_name}:</span>
                                                <span>
                                                    <span>₹${subFee.service_fees}</span>
                                                    <button class="btn btn-sm btn-primary ml-2 edit-btn" data-id="${subFee.id}" data-toggle="modal" data-target="#editServiceFeeModal_${subFee.id}">Edit</button>
                                                </span>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editServiceFeeModal_${subFee.id}" tabindex="-1" role="dialog" aria-labelledby="editServiceFeeModalLabel_${subFee.id}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editServiceFeeModalLabel_${subFee.id}">Edit Service Fee</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="post" action="{{ request()->segment(4) }}/servicefee/${subFee.id}/update">
                                                                    @csrf
                                                                    @method('put')
                                                                    <input type="hidden" id="editServiceFeeId_${subFee.id}" name="id" value="${subFee.id}">
                                                                    <div class="form-group">
                                                                        <label for="editServiceFeeName_${subFee.id}">Service Fee Name</label>
                                                                        <input type="text" class="form-control" id="editServiceFeeName_${subFee.id}" name="service_fees_name" value="${subFee.service_fees_name}" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="editServiceFeeAmount_${subFee.id}">Amount</label>
                                                                        <input type="text" class="form-control" id="editServiceFeeAmount_${subFee.id}" name="service_fees" value="${subFee.service_fees}" required>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                            </li>
                                            `;
                                });
                                accordionItem += `
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                `;
                                $('#accordionExample').append(accordionItem);

                                // **Moving the edit logic inside the loop to ensure subFee.id is accessible**


                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
                CallingServiceFees();



                // insertion of service fee here
                $('#createServiceFeeForm').submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('servicefee.store') }}',
                        data: $(this).serialize(),
                        success: function(response) {
                            console.log(response);
                            alert('Service Fee created successfully.');
                            CallingServiceFees();
                            // Optionally, you can redirect the user to another page after successful creation
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert('An error occurred. Please try again.');
                        }
                    });
                });

                // AJAX call to fetch data alongwith the edition work goes here:
                $.ajax({
                    url: '{{ route('service.show', request()->segment(4)) }}', // Update with your controller's URL
                    type: 'GET',
                    success: function(response) {
                        // Update the table with the response data
                        $("#service_id").val(response.id)

                        if (response) {
                            var reqNames = response.requirements.map(function(item) {
                                return item.req_name;
                            }).join(', ');
                            $(".viewtitle").html(response.name + " Service View")
                            let tableRows = `
                        <tr> 
                            <th>Id</th>
                            <td>${response.id}</td>
                        </tr>
                        <tr> 
                            <th>Name</th>
                            <td>${response.name}</td>
                        </tr>
                        <tr>
                            <th>Icon</th>
                            <td><img src='/uploads/${response.icon}' width='50px'/></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>${response.description}</td>
                        </tr>
                        <tr>
                            <th>Requirements</th>
                            <td>${reqNames}</td>
                        </tr>
                       
                            <tr>
                                <td> 
                                    <button class="btn btn-danger btn-sm mr-4 deleteBtn" data-id="${response.id}">Delete</button> 
                                    <button class="btn btn-primary btn-sm editBtn" data-id="${response.slug}">Edit</button>
                                     {{-- model for service-edit work goes here --}}
                                    <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" id="editServiceForm">
                                                    @csrf
                                                    @method('put')
                                                    <input type="hidden" id="editServiceId" name="id">
                                                    <div class="form-group">
                                                        <label for="editServiceName">Name</label>
                                                        <input type="text" class="form-control" id="editServiceName" name="name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editServiceDescription">Description</label>
                                                        <textarea class="form-control" id="editServiceDescription" name="description" required></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editServiceIcon">Icon</label>
                                                        <input type="file" class="form-control" id="editServiceIcon" name="icon">
                                                    </div>
                                                    <div class="form-group" id="editServiceRequirementsContainer">
                                                        // appending this in the ajax section with javascript appending method
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                </td>
                        </tr>`;
                            $('#tableBody').html(tableRows);
                        }
                        $(document).on('click', '.editBtn', function() {
                            const id = $(this).data('id'); // Get the service ID
                            const url = `/api/admin/service/${id}`; // Construct the API URL

                            // Fetch the service data via AJAX
                            $.ajax({
                                url: url,
                                type: 'GET',
                                success: function(response) {
                                    // Debug the requirements structure
                                    // console.log(response.requirements);

                                    // Populate the modal fields with the response data
                                    $('#editServiceId').val(response.slug);
                                    $('#editServiceName').val(response.name);
                                    $('#editServiceDescription').val(response
                                        .description);

                                    // Process requirements safely  
                                    if (Array.isArray(response.requirements)) {
                                        let requirementsHTML =
                                            ''; // To hold the dynamically created input fields

                                        response.requirements.forEach(function(item,
                                            index) {
                                            requirementsHTML += `
                                                <div class="requirement-item">
                                                    <label for="requirement-${index}">Requirement ${index + 1}</label>
                                                    <input type="text" class="form-control" id="requirement-${index}" name="requirements[${index}][req_name]" value="${item.req_name}" data-id="${item.id}">
                                                </div>`;
                                        });

                                        $('#editServiceRequirementsContainer').html(
                                            requirementsHTML);
                                        console.log(response
                                            .requirements); // Log for debugging
                                    } else {
                                        console.error('Invalid requirements data.');
                                        $('#editServiceRequirementsContainer').html(
                                            ''); // Clear the field if invalid
                                    }

                                    // Open the modal
                                    $('#editServiceModal').modal('show');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                    alert('Failed to fetch service details.');
                                }
                            });

                        });

                        //edit work goes here
                        $('#editServiceForm').submit(function(e) {
                            e.preventDefault(); // Prevent the default form submission

                            const id = $('#editServiceId').val(); // Get the service ID
                            const updateUrl = `/api/admin/service/${id}`; // Update API URL

                            let name = $('#editServiceName').val();
                            let description = $('#editServiceDescription').val();
                            // let requirements = $('#editServiceRequirements').val();


                            // Collect requirements as an array of objects
                            let requirements = [];
                            $('#editServiceRequirementsContainer .requirement-item input').each(
                                function() {
                                    requirements.push({
                                        id: $(this).data('id'), //here fetching the ID from requirements;
                                        req_name: $(this).val().trim()
                                    });
                                });



                            // Send the update request via AJAX
                            let formdata = {
                                name,
                                description,
                                requirements
                            };

                            console.log(formdata);
                            $.ajax({
                                url: updateUrl,
                                type: 'PUT',
                                data: JSON.stringify(
                                    formdata), // Convert to JSON string for proper handling
                                contentType: 'application/json', // Specify the content type as JSON
                                success: function(response) {
                                    alert('Service updated successfully.');
                                    $('#editServiceModal').modal(
                                        'hide'); // Close the modal
                                    location.href =
                                        "/admin/service/"; // Reload the page to reflect changes
                                },
                                error: function(xhr) {
                                    let errorMessage =
                                        'Failed to update service. Please try again.';
                                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                                .errors)
                                            .flat()
                                            .join('\n');
                                    }
                                    alert(errorMessage);
                                },
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                // here goes the services and the required fee:
                $.ajax({
                    url: '{{ route('servicefee.index') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        "service_id": service_id
                    },
                    success: function(response) {
                        // Populate the selected dropdown with service fees
                        var select = $('#parent_id');
                        select.empty();
                        select.append($('<option>').text('Main Category').attr('value', ""));
                        $.each(response, function(index, item) {
                            select.append($('<option>').text(item.service_fees_name).attr('value',
                                item.id));
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

                // AJAX call to delete item
                $(document).on('click', '.deleteBtn', function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: '/api/admin/service/' + id,
                        type: 'DELETE',
                        success: function(response) {
                            window.location.href = "{{ route('admin.service.manage') }}"
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });

            });
        </script>
    @endsection
