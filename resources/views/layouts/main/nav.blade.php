
@if(Auth::user()->r_es_super_admin == true)


<nav id="sidebar" aria-label="Main Navigation" style="border-right: 1px solid #acacac;">

    <!-- Side Header -->
    <div class="content-header">
        <!-- Logo -->
        <a class="font-semibold text-dual" href="javascript:void(0)" style="margin: 0 auto;">
            <span class="smini-visible">
                <i class="fa fa-circle-notch text-primary"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">
                <img src="{{ asset('/images/640px-Orange_Bank_2017.png') }}"
                style="height: 50px;" alt="" srcset="" >
            </span>
        </a>
        <!-- END Logo -->

        <!-- Extra -->
        <div>
            <!-- Dark Mode -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            {{-- <a class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="dark_mode_toggle" href="javascript:void(0)">
                <i class="far fa-moon"></i>
            </a> --}}
            <!-- END Dark Mode -->


            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
            <!-- END Close Sidebar -->
        </div>
        <!-- END Extra -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        <div class="content-side">


            <ul class="nav-main">

                <li class="nav-main-item" >
                    <a class="nav-main-link{{ request()->is('accueil') ? ' active' : '' }}" href="{{ url('/accueil') }}">
                        <i class="nav-main-link-icon si si-home text-primary"></i>
                        <span class="nav-main-link-name">Tableau de bord</span>
                    </a>
                </li>

                <li class="nav-main-item{{ request()->is('typeclient') ? ' open' : '' }}
                    {{ request()->is('produits') ? ' open' : '' }} {{ request()->is('entreprise') ? ' open' : '' }}" >
                    {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                            <i class="nav-main-link-icon far fa-share-from-square text-primary"></i>
                            <span class="nav-main-link-name">Configuration</span>
                        </a>
                        <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('typeclient') ? ' active' : '' }}" href="{{ url('/typeclient') }}">
                                    {{--                                     <i class="nav-main-link-icon fa fa-users-viewfinder"></i>
                                    --}}                                    <span class="nav-main-link-name">Type de clients</span>
                                </a>
                            </li>

                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('produits') ? ' active' : '' }}" href="{{ url('/produits') }}">
                                    {{--                                     <i class="nav-main-link-icon fa fa-basket-shopping"></i>
                                    --}}                                    <span class="nav-main-link-name">Produits</span>
                                </a>
                            </li>

                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('entreprise') ? ' active' : '' }}" href="{{ url('/entreprise') }}">
                                    {{-- <i class="nav-main-link-icon fa fa-user-tie"></i> --}}
                                    <span class="nav-main-link-name">Entreprises</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-main-item{{ request()->is('typechamps') ? ' open' : '' }}
                        {{ request()->is('champs') ? ' open' : '' }}
                        {{ request()->is('gpechamps') ? ' open' : '' }}
                        {{ request()->is('formulaires') ? ' open' : '' }}">
                        {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                <i class="nav-main-link-icon fa fa-file-lines text-primary"></i>
                                <span class="nav-main-link-name">&nbsp; Formulaires</span>
                            </a>

                            <ul class="nav-main-submenu">

                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('typechamps') ? ' active' : '' }}" href="{{ url('/typechamps') }}">
                                        <span class="nav-main-link-name">Type de champs</span>
                                    </a>
                                </li>


                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('champs') ? ' active' : '' }}" href="{{ url('/champs') }}">
                                        <span class="nav-main-link-name">Champs</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('gpechamps') ? ' active' : '' }}" href="{{ url('/gpechamps') }}">
                                        <span class="nav-main-link-name">Groupe de champs</span>
                                    </a>
                                </li>

                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('formulaires') ? ' active' : '' }}" href="{{ url('/formulaires') }}">
                                        <span class="nav-main-link-name">Formulaire</span>
                                    </a>
                                </li>


                            </ul>
                        </li>

                        <li class="nav-main-item{{ request()->is('workflows*') ? ' open' : '' }} {{ request()->is('utilisateurs/validateurs') ? ' open' : '' }}">
                            {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                    <i class="nav-main-link-icon fa fa-gears text-primary"></i>
                                    <span class="nav-main-link-name">Workflows</span>
                                </a>
                                <ul class="nav-main-submenu">
                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('utilisateurs/validateurs') ? ' active' : '' }}" href="{{ url('/utilisateurs/validateurs') }}">
                                            <span class="nav-main-link-name">Validateurs</span>
                                        </a>
                                    </li>

                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('workflows') ? ' active' : '' }}" href="{{ url('/workflows') }}">
                                            <span class="nav-main-link-name">Configuration</span>
                                        </a>
                                    </li>

                                    <li class="nav-main-item" hidden>
                                        <a class="nav-main-link{{ request()->is('workflows') ? ' active' : '' }}" href="{{ url('/workflows') }}">
                                            <span class="nav-main-link-name">Suivie Workflows</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-main-item{{ request()->is('utilisateurs') ? ' open' : '' }}
                                {{ request()->is('roles') ? ' open' : '' }} {{ request()->is('permissions') ? ' open' : '' }}">
                                {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-users text-primary"></i>
                                        <span class="nav-main-link-name">Utilisateurs</span>
                                    </a>

                                    <ul class="nav-main-submenu">
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('roles') ? ' active' : '' }}" href="{{ url('/roles') }}">
                                                <span class="nav-main-link-name">Rôles</span>
                                            </a>
                                        </li>

                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('permissions') ? ' active' : '' }}" href="{{ url('/permissions') }}">
                                                <span class="nav-main-link-name">Permissions</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('utilisateurs') ? ' active' : '' }}" href="{{ url('/utilisateurs') }}">
                                                <span class="nav-main-link-name">Utilisateurs</span>
                                            </a>
                                        </li>



                                    </ul>
                                </li>

                            </ul>
                        </div>
                        <!-- END Side Navigation -->
                    </div>
                    <!-- END Sidebar Scrolling -->
                </nav>

@else

<nav id="sidebar" aria-label="Main Navigation" style="border-right: 1px solid #acacac;">

    <!-- Side Header -->
    <div class="content-header">
        <!-- Logo -->
        <a class="font-semibold text-dual" href="javascript:void(0)" style="margin: 0 auto;">
            <span class="smini-visible">
                <i class="fa fa-circle-notch text-primary"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">
                <img src="{{ asset('/images/640px-Orange_Bank_2017.png') }}"
                style="height: 50px;" alt="" srcset="" >
            </span>
        </a>
        <!-- END Logo -->

        <!-- Extra -->
        <div>
            <!-- Dark Mode -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            {{-- <a class="btn btn-sm btn-alt-secondary" data-toggle="layout" data-action="dark_mode_toggle" href="javascript:void(0)">
                <i class="far fa-moon"></i>
            </a> --}}
            <!-- END Dark Mode -->


            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
            <!-- END Close Sidebar -->
        </div>
        <!-- END Extra -->
    </div>
    <!-- END Side Header -->

    <!-- Sidebar Scrolling -->
    <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
        <div class="content-side">


            <ul class="nav-main">

                @foreach ($permissions as $permission)
                @if($permission->slug == 'tdb')
                <li class="nav-main-item" >
                    <a class="nav-main-link{{ request()->is('accueil') ? ' active' : '' }}" href="{{ url('/accueil') }}">
                        <i class="nav-main-link-icon si si-home text-primary"></i>
                        <span class="nav-main-link-name">Tableau de bord</span>
                    </a>
                </li>
                @endif
                @endforeach

                @foreach ($permissions as $permission)
                @if($permission->slug == 'config')
                <li class="nav-main-item{{ request()->is('typeclient') ? ' open' : '' }}
                    {{ request()->is('produits') ? ' open' : '' }} {{ request()->is('entreprise') ? ' open' : '' }}" >
                    {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                            <i class="nav-main-link-icon far fa-share-from-square text-primary"></i>
                            <span class="nav-main-link-name">Configuration</span>
                        </a>
                        <ul class="nav-main-submenu">

                            @foreach ($permissions as $permission)
                            @if($permission->slug == 'typeclient')
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('typeclient') ? ' active' : '' }}" href="{{ url('/typeclient') }}">
                                    {{--                                     <i class="nav-main-link-icon fa fa-users-viewfinder"></i>
                                    --}}                                    <span class="nav-main-link-name">Type de clients</span>
                                </a>
                            </li>
                            @endif
                            @endforeach

                            @foreach ($permissions as $permission)
                            @if($permission->slug == 'prd')
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('produits') ? ' active' : '' }}" href="{{ url('/produits') }}">
                                    {{--                                     <i class="nav-main-link-icon fa fa-basket-shopping"></i>
                                    --}}                                    <span class="nav-main-link-name">Produits</span>
                                </a>
                            </li>
                            @endif
                            @endforeach

                            @foreach ($permissions as $permission)
                            @if($permission->slug == 'entp')
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ request()->is('entreprise') ? ' active' : '' }}" href="{{ url('/entreprise') }}">
                                    {{-- <i class="nav-main-link-icon fa fa-user-tie"></i> --}}
                                    <span class="nav-main-link-name">Entreprises</span>
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </li>
                    @endif
                    @endforeach



                    @foreach ($permissions as $permission)
                    @if($permission->slug == 'config-forms')
                    <li class="nav-main-item{{ request()->is('typechamps') ? ' open' : '' }}
                        {{ request()->is('champs') ? ' open' : '' }}
                        {{ request()->is('gpechamps') ? ' open' : '' }}
                        {{ request()->is('formulaires') ? ' open' : '' }}">
                        {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                <i class="nav-main-link-icon fa fa-file-lines text-primary"></i>
                                <span class="nav-main-link-name">&nbsp; Formulaires</span>
                            </a>

                            <ul class="nav-main-submenu">

                                @can('isAdmin')
                                @foreach ($permissions as $permission)
                                @if($permission->slug == 'typechamps')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('typechamps') ? ' active' : '' }}" href="{{ url('/typechamps') }}">
                                        <span class="nav-main-link-name">Type de champs</span>
                                    </a>
                                </li>
                                @endif
                                @endforeach

                                @foreach ($permissions as $permission)
                                @if($permission->slug == 'champs')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('champs') ? ' active' : '' }}" href="{{ url('/champs') }}">
                                        <span class="nav-main-link-name">Champs</span>
                                    </a>
                                </li>
                                @endif
                                @endforeach

                                @foreach ($permissions as $permission)
                                @if($permission->slug == 'gpechps')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('gpechamps') ? ' active' : '' }}" href="{{ url('/gpechamps') }}">
                                        <span class="nav-main-link-name">Groupe de champs</span>
                                    </a>
                                </li>
                                @endif
                                @endforeach

                                @endcan

                                @foreach ($permissions as $permission)
                                @if($permission->slug == 'forms')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ request()->is('formulaires') ? ' active' : '' }}" href="{{ url('/formulaires') }}">
                                        <span class="nav-main-link-name">Formulaire</span>
                                    </a>
                                </li>
                                @endif
                                @endforeach

                            </ul>
                        </li>
                        @endif
                        @endforeach

                        @foreach ($permissions as $permission)
                        @if($permission->slug == 'wkf')
                        <li class="nav-main-item{{ request()->is('workflows*') ? ' open' : '' }} {{ request()->is('utilisateurs/validateurs') ? ' open' : '' }}">
                            {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                    <i class="nav-main-link-icon fa fa-gears text-primary"></i>
                                    <span class="nav-main-link-name">Workflows</span>
                                </a>
                                <ul class="nav-main-submenu">
                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('utilisateurs/validateurs') ? ' active' : '' }}" href="{{ url('/utilisateurs/validateurs') }}">
                                            <span class="nav-main-link-name">Validateurs</span>
                                        </a>
                                    </li>
                                    @foreach ($permissions as $permission)
                                    @if($permission->slug == 'config-work')
                                    <li class="nav-main-item">
                                        <a class="nav-main-link{{ request()->is('workflows') ? ' active' : '' }}" href="{{ url('/workflows') }}">
                                            <span class="nav-main-link-name">Configuration</span>
                                        </a>
                                    </li>
                                    @endif
                                    @endforeach

                                    <li class="nav-main-item" hidden>
                                        <a class="nav-main-link{{ request()->is('workflows') ? ' active' : '' }}" href="{{ url('/workflows') }}">
                                            <span class="nav-main-link-name">Suivie Workflows</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            @endforeach

                            @foreach ($permissions as $permission)
                            @if($permission->slug == 'user')
                            <li class="nav-main-item{{ request()->is('utilisateurs') ? ' open' : '' }}
                                {{ request()->is('roles') ? ' open' : '' }} {{ request()->is('permissions') ? ' open' : '' }}">
                                {{-- <li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}"> --}}
                                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                                        <i class="nav-main-link-icon fa fa-users text-primary"></i>
                                        <span class="nav-main-link-name">Utilisateurs</span>
                                    </a>

                                    <ul class="nav-main-submenu">
                                        @can('isAdmin')
                                        @foreach ($permissions as $permission)
                                        @if($permission->slug == 'role')
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('roles') ? ' active' : '' }}" href="{{ url('/roles') }}">
                                                <span class="nav-main-link-name">Rôles</span>
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach

                                        @foreach ($permissions as $permission)
                                        @if($permission->slug == 'permissions')
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('permissions') ? ' active' : '' }}" href="{{ url('/permissions') }}">
                                                <span class="nav-main-link-name">Permissions</span>
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach

                                        @endcan

                                        @foreach ($permissions as $permission)
                                        @if($permission->slug == 'utilisateurs')
                                        <li class="nav-main-item">
                                            <a class="nav-main-link{{ request()->is('utilisateurs') ? ' active' : '' }}" href="{{ url('/utilisateurs') }}">
                                                <span class="nav-main-link-name">Utilisateurs</span>
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach


                                    </ul>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                        <!-- END Side Navigation -->
                    </div>
                    <!-- END Sidebar Scrolling -->
                </nav>



@endif
