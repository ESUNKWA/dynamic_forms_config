@extends('layouts.backend')

@section('content')
  <!-- breadcrumb -->
  @include('layouts.main.breadcrumb', ['test' => "salut"])
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <!-- Your Block -->
    <div class="block block-rounded">
      <div class="block-header block-header-default">
        <h3 class="block-title">
          Block Title
        </h3>
      </div>
      <div class="block-content">
        <p>Your content..</p>
      </div>
    </div>
    <!-- END Your Block -->
  </div>
  <!-- END Page Content -->
@endsection
