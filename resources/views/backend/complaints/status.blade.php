@extends('layout.base')

@section('content')
    <div class="content">
                    <!-- Start Content-->
                    <div class="container-fluid">
                        <div class="row page-title">
                            <div class="col-md-12">
                                <nav aria-label="breadcrumb" class="float-right mt-1">
                                </nav>
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('message') }}</p>
                                    @endif

                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <h4 class="mb-1 mt-0">Submit a Complaint</h4>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        
                                        <h4 class="header-title mt-0 mb-1">Complaint Submission</h4>
                                        <p class="sub-header">
                                            Please enter your details carefully and click send to submit your complaint
                                        </p>

                                        <form method="post" action="{{route('complaint.update', $response->data->complaint->_id)}}">
                                            
                                            <input type="hidden" name="_method" value="PUT" />
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                            <h5>Log your Complain</h5><br>
                                            <div class="col">
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label"
                                                            for="simpleinput">Full Name</label>
                                                        <div class="col-lg-10">
                                                            <input type="text" class="form-control" id="simpleinput" readonly
                                                                value="{{ $response->data->complaint->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label"
                                                            for="example-email">Email</label>
                                                        <div class="col-lg-10">
                                                            <input type="email" id="example-email"
                                                                class="form-control"  readonly value="{{ $response->data->complaint->email }}">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label"
                                                            for="example-textarea">Message</label>
                                                        <div class="col-lg-10">
                                                            <textarea class="form-control" rows="5"
                                                                id="example-textarea" readonly placeholder="Please enter your complaint here">{{ $response->data->complaint->message }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label"
                                                            for="example-textarea">Message</label>
                                                        <div class="col-lg-10">
                                                        <select data-plugin="customselect" name="cc" class="form-control">
                                                        <option name="cc" value="New">New</option>
                                                        <option name="cc" value="Investigating">Investigating</option>
                                                        <option name="cc" value="Resolved">Resolved</option>
                                                    </select>
                                                        </div>
                                                    </div>
                                                    <div class="float-right">
                                            <button class="btn btn-primary">Update</button>
                                        </div>
                                                                      
    </form>
                                        <form method="post" action="{{ route('complaint.index') }}">

                                        <div>
                                            <button class="btn btn-danger">Cancel</button>
                                        </div>
                                        </form>
            
                                    </div> <!-- end card-body -->
                                </div> <!-- end card-->
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->

                        <!-- end col -->
                        </div>
                        <!-- end row -->
                        
                    </div> <!-- container-fluid -->

                </div> 

            </div>
@endsection
