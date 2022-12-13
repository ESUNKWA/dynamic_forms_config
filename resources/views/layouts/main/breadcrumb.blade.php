<div class="bg-body-light">
    <div class="container-fluid">
      <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-0">
        <div class="flex-grow-1">
          <h6 class="h6 fw-bold mb-2">
            {{ $titre }}
          </h6>
          <h6 class="fs-base lh-base fw-medium text-muted mb-0">
            {{ $soustitre }}
          </h6>
        </div>
        <nav class="flex-shrink-0 m-3 mt-sm-0 ms-sm-0" aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-alt">
            <li class="breadcrumb-item">
              <a class="link-fx" href="javascript:void(0)">{{ $chemin }}</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                {{ $titre }}
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
