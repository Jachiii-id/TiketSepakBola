@extends('layouts.app-plain')

@section('title', 'Dashboard - Tactick')

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet" />
    <link href="{{ asset('assets/css/dashboard/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dashboard/nucleo-svg.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link id="pagestyle" href="{{ asset('assets/css/dashboard/material-dashboard.css') }}" rel="stylesheet" />

@endsection

@section('content')

    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol>
        </nav>
       
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="ms-3">
          <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
          <p class="mb-4">
            Data statistik permasalahan penjualan tiket sepak bola di Indonesia
          </p>
    </div>
      <div class="row">
            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Jumlah Likes</p>
                                    <h4 class="mb-0">{{ $totalLikes }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Jumlah Komen</p>
                                    <h4 class="mb-0">{{ $totalComments }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Jumlah Kota</p>
                                    <h4 class="mb-0">{{ $uniqueLocations }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
      <div class="row">
            <!-- Card 1 -->
            <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-0">Perbandingan Jenis Postingan Gambar atau Video</h6>
                        <div class="pe-2">
                            <div class="chart">
                                <canvas id="chart-pie" class="chart-canvas"></canvas>
                            </div>
                        </div>
                        <hr class="dark horizontal">
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-0">Jumlah likes dan comment di setiap hashtag</h6>
                        <div class="pe-2">
                            <div class="chart">
                                <canvas id="chart-bar" class="chart-canvas"></canvas>
                            </div>
                        </div>
                        <hr class="dark horizontal">
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-xl-4 col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-0">Lokasi Populer Berdasarkan Interaksi</h6>
                        <div class="pe-2">
                            <div class="chart">
                                <canvas id="line-chart" style="width: 100%; height: 300px;"></canvas>
                            </div>
                        </div>
                        <hr class="dark horizontal">             
                    </div>
                </div>
            </div>
        </div>

      <div class="row">
          <div class="col-lg-12 col-md-12">
              <div class="card">
                  <!-- Card Header -->
                  <div class="card-header pb-0">
                      <div class="row">
                          <div class="col-lg-6 col-md-6">
                              <h3 class="font-weight-bolder">List Postingan</h3>
                          </div>
                        <hr>
                      </div>
                  </div>
                  <!-- End Card Header -->

                  <!-- Card Body -->
                  <div class="card-body px-0 pb-2">
                      <div class="table-responsive">
                          <table class="table align-items-center mb-0">
                              <!-- Table Header -->
                              <thead>
                                  <tr>
                                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hashtag</th>
                                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Post URL</th>
                                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Like Count</th>
                                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comment Count</th>
                                  </tr>
                              </thead>
                              <!-- Table Body -->
                              <tbody>
                                  @forelse($lists as $list)
                                      <tr>
                                          <td class="align-middle text-center text-sm">
                                              <span class="text-xs font-weight-bold">{{ $list->hashtag }}</span>
                                          </td>
                                          <td class="align-middle text-center text-sm">
                                              <a href="{{ $list->post_url }}" target="_blank" class="text-xs text-primary font-weight-bold">
                                                  Lihat Post
                                              </a>
                                          </td>
                                          <td class="align-middle text-center text-sm">
                                              <span class="text-xs font-weight-bold">{{ number_format($list->like_count) }}</span>
                                          </td>
                                          <td class="align-middle text-center text-sm">
                                              <span class="text-xs font-weight-bold">{{ number_format($list->comment_count) }}</span>
                                          </td>
                                      </tr>
                                  @empty
                                      <tr>
                                          <td colspan="4" class="text-center text-sm text-muted">
                                              <span class="text-xs">Tidak ada data postingan</span>
                                          </td>
                                      </tr>
                                  @endforelse
                              </tbody>
                          </table>
                      </div>

                      <!-- Pagination -->
                      <div class="d-flex justify-content-center mt-4">
                          {{ $lists->links('pagination::bootstrap-4') }}
                      </div>
                  </div>
                  <!-- End Card Body -->
              </div>
          </div>
      </div>
    </div>
    </div>
@endsection

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById("chart-pie").getContext("2d");

            // Data JSON dari PHP
            var postTypeData = <?php echo json_encode($postTypeCounts); ?>;

            // Inisialisasi Chart.js
            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ["Image", "Video"], // Label untuk setiap jenis postingan
                    datasets: [{
                        label: "Jenis Postingan",
                        data: [postTypeData.image, postTypeData.video], // Mengakses data image dan video
                        backgroundColor: ["#43A047", "#FF9800"], // Warna untuk setiap kategori
                        hoverBackgroundColor: ["#2E7D32", "#E65100"], // Warna ketika di-hover
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom", // Posisi legenda
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                    const percentage = ((context.raw / total) * 100).toFixed(2);
                                    return `${context.label}: ${context.raw} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById("chart-bar").getContext("2d");

            // Data dari PHP
            var hashtagData = {
                hashtags: <?php echo json_encode($hashtags); ?>,
                likes: <?php echo json_encode($likes); ?>,
                comments: <?php echo json_encode($comments); ?>
            };

            // Inisialisasi Bar Chart
            new Chart(ctx, {
                type: 'bar',  // Tipe chart adalah bar chart
                data: {
                    labels: hashtagData.hashtags,  // Label adalah hashtag
                    datasets: [{
                        label: 'Likes',  // Label untuk dataset pertama (Likes)
                        data: hashtagData.likes,  // Data jumlah likes per hashtag
                        backgroundColor: '#4CAF50',  // Warna batang untuk likes
                        borderColor: '#388E3C',  // Warna border batang untuk likes
                        borderWidth: 1
                    }, {
                        label: 'Comments',  // Label untuk dataset kedua (Comments)
                        data: hashtagData.comments,  // Data jumlah comments per hashtag
                        backgroundColor: '#FF9800',  // Warna batang untuk comments
                        borderColor: '#F57C00',  // Warna border batang untuk comments
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,  // Responsif agar chart menyesuaikan ukuran layar
                    scales: {
                        x: {
                            beginAtZero: true  // Mulai sumbu X dari 0
                        },
                        y: {
                            beginAtZero: true  // Mulai sumbu Y dari 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,  // Menampilkan legend di chart
                            position: 'top'  // Posisi legend di bagian atas
                        }
                    }
                }
            });
        });
    </script>

<script>
    // Pastikan data dikirim dengan benar dari PHP ke JavaScript
    var locationsData = @json($locationsData);  // Mengonversi data PHP ke JSON

    document.addEventListener("DOMContentLoaded", function () {
        var cities = locationsData.map(data => data.city);
        var likes = locationsData.map(data => data.total_likes);
        var comments = locationsData.map(data => data.total_comments);

        var ctx = document.getElementById("line-chart").getContext("2d");
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: cities,
                datasets: [
                    {
                        label: 'Total Likes',
                        data: likes,
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Total Comments',
                        data: comments,
                        borderColor: '#FF9800',
                        backgroundColor: 'rgba(255, 152, 0, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Kota',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Interaksi',
                        }
                    }
                }
            }
        });
    });
</script>
