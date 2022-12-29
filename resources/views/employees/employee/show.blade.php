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
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#contact">Kontak</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#family">Keluarga</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#education">Pendidikan</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#position">Jabatan</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#training">Pelatihan</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#work">Pekerjaan</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#asset">Aset</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#file">File</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="identity">
                    @include('employees.employee.identity-index')
                </div>
                <div class="tab-pane fade" id="contact">
                    @include('employees.employee.contact-index')
                </div>
                <div class="tab-pane fade" id="family">
                    @include('employees.employee.family-index')
                </div>
                <div class="tab-pane fade" id="education">
                    @include('employees.employee.education-index')
                </div>
                <div class="tab-pane fade" id="position">
                    @include('employees.employee.position-index')
                </div>
                <div class="tab-pane fade" id="training">
                    @include('employees.employee.training-index')
                </div>
                <div class="tab-pane fade" id="work">
                    @include('employees.employee.work-index')
                </div>
                <div class="tab-pane fade" id="asset">
                    @include('employees.employee.asset-index')
                </div>
                <div class="tab-pane fade" id="file">
                    @include('employees.employee.file-index')
                </div>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
