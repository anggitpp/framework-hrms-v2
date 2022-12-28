@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <div class="card-body pt-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x fs-5 fw-bold mb-5">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#identity">Identitas</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#family">Keluarga</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#education">Pendidikan</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="identity">
                    @include('employees.employee.identity-index')
                </div>
                <div class="tab-pane fade" id="family">
                    @include('employees.employee.family-index')
                </div>
                <div class="tab-pane fade" id="education">
                    @include('employees.employee.education-index')
                </div>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
