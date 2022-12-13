@extends('layouts.simple')

@section('content')
  <!-- Hero -->
  <div class="hero bg-body-extra-light overflow-hidden">
    <div class="hero-inner">
      <div class="content content-full text-center">
        <h1 class="fw-bold mb-2">
          Workflow | &nbsp;<span style="color: #ff7900">O</span>range <span style="color: #ff7900">B</span>ank <span style="color: #ff7900">A</span>frica
        </h1>
        <p class="fs-lg fw-medium text-muted mb-4">
          Système centralisé
        </p>
        <a class="btn btn-alt-primary px-3 py-2" href="{{ route('login') }}">
          Continuer
          <i class="fa fa-fw fa-arrow-right opacity-50 ms-1"></i>
        </a>
      </div>
    </div>
  </div>
  <!-- END Hero -->
@endsection
