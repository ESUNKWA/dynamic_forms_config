@extends('layouts.backend')

@section('js_after')
<!-- jQuery (required for DataTables plugin) -->
<script src="{{ asset('js/lib/jquery.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

$(function(){

});

    var month = [], demandes = [];

    fetch('{{ url("/oba/dashsc") }}')
    .then(response => response.json())
    .then((res) => {

        const mois = [];
        month = res.stat_demande_mois.mois;
        demandes = res.stat_demande_mois.total_demande;

        $('#total_produit').append(res.total_produit);
        $('#total_client').append(res.total_client);
        $('#total_convention').append(res.total_conv);
        $('#total_users_sc').append(res.total_users_back);

        const labels = [
        'Janvier',
        'Févier',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre'
        ];

        month.forEach((el) => {
            mois.push(labels[el-1]);
        });

        const data = {
            labels: mois,
            datasets: [{
                label: 'Nombre de demande par mois',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: demandes,
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {}
        };
        const myChart = new Chart(
        document.getElementById('myChart'),
        config
        );
    });



</script>


@endsection

@section('content')
<!-- breadcrumb -->
@include('layouts.main.breadcrumb', [
'titre' => 'Tableau de bord',
'soustitre' => 'Tableau de bord',
'chemin' => ""
])
<!-- END Hero -->

<!-- Page Content -->

<div class="content">
    <!-- Overview -->
    <div class="row items-push">
        <div class="col-sm-6 col-xxl-3">
            <!-- Pending Orders -->
            <div class="block block-rounded d-flex flex-column h-100 mb-0" style="background-color: #FFE8F7">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold" id="total_produit"></dt>
                        <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Nombre total de produit</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="far fa-gem fs-3 text-primary"></i>
                    </div>
                </div>
                <div class="bg-body-light rounded-bottom">
                    <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between" href="{{ url('/produits') }}">
                        <span>Voir les produits</span>
                        <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                    </a>
                </div>
            </div>
            <!-- END Pending Orders -->
        </div>
        <div class="col-sm-6 col-xxl-3">
            <!-- New Customers -->
            <div class="block block-rounded d-flex flex-column h-100 mb-0" style="background-color: #B8EBD6">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold" id="total_client" ></dt>
                        <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Nombre total de client</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="fa fa-user-plus fs-3 text-primary"></i>
                    </div>
                </div>
                <div class="bg-body-light rounded-bottom">
                    <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between" href="#">
                        <span></span>
                        <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                    </a>
                </div>
            </div>
            <!-- END New Customers -->
        </div>
        <div class="col-sm-6 col-xxl-3">
            <!-- Messages -->
            <div class="block block-rounded d-flex flex-column h-100 mb-0" style="background-color: #D9C2F0">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold" id="total_convention"></dt>
                        <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">total conventions</dd>
                    </dl>
                    <div class="item item-rounded-lg bg-body-light">
                        <i class="far fa-paper-plane fs-3 text-primary"></i>
                    </div>
                </div>
                <div class="bg-body-light rounded-bottom">
                    <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between" href="javascript:void(0)">
                        <span>Voir les conventions</span>
                        <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                    </a>
                </div>
            </div>
            <!-- END Messages -->
        </div>
        <div class="col-sm-6 col-xxl-3">
            <!-- Conversion Rate -->
            <div class="block block-rounded d-flex flex-column h-100 mb-0" style="background-color: #FFF6B6">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="fs-3 fw-bold" id="total_users_sc" ><dt>
                            <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">total backoffice</dd>
                        </dl>
                        <div class="item item-rounded-lg bg-body-light">
                            <i class="fa fa-users fs-3 text-primary"></i>
                        </div>
                    </div>
                    <div class="bg-body-light rounded-bottom">
                        <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between" href="{{ url('/utilisateurs') }}">
                            <span>Voir les utilisateurs</span>
                            <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                        </a>
                    </div>
                </div>
                <!-- END Conversion Rate-->
            </div>
        </div>
        <!-- END Overview -->

        <!-- Statistics -->
        <div class="row">
            <div class="col-xl-8 d-flex flex-column">
                <div>
                    <canvas id="myChart"></canvas>
                </div>


                <!-- END Earnings Summary -->
            </div>
            <div class="col-xl-4 d-flex flex-column">


            </div>
                    <!-- END Recent Orders -->
        </div>

    </div>
</div>
        <!-- END Statistics -->


            <!-- END Page Content -->
@endsection
