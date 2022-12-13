<aside id="side-overlay" class="fs-sm">
    <!-- Side Header -->
    <div class="content-header border-bottom">
        <!-- User Avatar -->
        <a class="img-link me-1" href="javascript:void(0)">
            <img class="img-avatar img-avatar32" src=" @if(Auth::user()->path_name)
            {{ Auth::user()->path_name }}
            @else
            {{ url('storage/images/utilisateurs/default_photo.png') }}
            @endif  "alt="">
        </a>
        <!-- END User Avatar -->

        <!-- User Info -->
        <div class="ms-2">
            <a class="text-dark fw-semibold fs-sm" href="javascript:void(0)">{{ Auth::user()->name }} {{ Auth::user()->lastname }}</a>
        </div>
        <!-- END User Info -->

        <!-- Close Side Overlay -->
        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
        <a class="ms-auto btn btn-sm btn-alt-danger" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_close">
            <i class="fa fa-fw fa-times"></i>
        </a>
        <!-- END Close Side Overlay -->
    </div>
    <!-- END Side Header -->

    <!-- Side Content -->
    <div class="content-side">
        <ul class="nav nav-pills flex-column push">
            <li class="nav-item mb-1">
              <a class="nav-link" href="{{ url('/monprofil') }}">
                <i class="fa fa-fw fa-user me-1"></i> Mon profil
              </a>
            </li>
            <li class="nav-item mb-1">
              <a class="nav-link" href="javascript:void(0)">
                <i class="fa fa-fw fa-pencil-alt me-1"></i> Changer mes accès
              </a>
            </li>
          </ul>



        <hr>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link :href="route('logout')"
            onclick="event.preventDefault();
            this.closest('form').submit();">
            {{ __('Se déconnecter') }}

        </x-dropdown-link>


    </form>

</div>
<!-- END Side Content -->
</aside>
