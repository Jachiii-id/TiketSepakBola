@extends('layouts.app-plain')

@section('title', 'Tiket - Tactick')

@section('content')
    <!-- Page Content  -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                @include('partials.breadcrumb')
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            {{-- <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                                class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                                aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                                aria-label="Slide 3"></button> --}}
                            @foreach ($images as $index => $image)
                                <button type="button" data-bs-target="#carouselExampleCaptions"
                                    data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"
                                    aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->image_link }}" class="d-block" alt="{{ $image->alt }}">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>{{ $image->short_desc }}</h5>
                                        {{-- <p>{{ $image->long_desc }}</p> --}}
                                    </div>
                                </div>
                            @endforeach
                            {{-- <div class="carousel-item active">
                                <img src="https://www.footballticketnet.fr/theme/images/special_picture/Chelsea-Special-Picture-FootballTicketNet.jpg.webp?cb=7130"
                                    class="d-block">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>First slide label</h5>
                                    <p>Some representative placeholder content for the first slide.</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://www.footballticketnet.fr/theme/images/special_picture/Liverpool-Special-Picture-FootballTicketNet.jpg.webp?cb=7130"
                                    class="d-block">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Second slide label</h5>
                                    <p>Some representative placeholder content for the second slide.</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="https://www.footballticketnet.fr/theme/images/special_picture/FC-Barcelona-Special-Picture-FootballTicketNet.jpg.webp?cb=7130"
                                    class="d-block">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>Third slide label</h5>
                                    <p>Some representative placeholder content for the third slide.</p>
                                </div>
                            </div> --}}
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Match</th>
                                        <th class="text-center"></th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matches as $match)
                                        <tr>
                                            <td class="col-lg-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="media-body ms-3">
                                                        <h6 class="mb-0">{{ $match->date }} <br> {{ $match->time }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="col-lg-2">
                                                <img class="img-fluid rounded-circle avatar-40"
                                                    src="{{ $match->getClub1->logo }}" alt="">
                                                VS
                                                <img class="img-fluid rounded-circle avatar-40"
                                                    src="{{ $match->getClub2->logo }}" alt="">
                                            </td>
                                            <td class="col-lg-5">
                                                <div class="media-body ms3">
                                                    <h6 class="mb-0">
                                                        <p class="mb-0">{{ $match->name }}</p>
                                                        <p class="text-dark">{{ $match->description }}</p>
                                                    </h6>
                                                </div>
                                            </td>
                                            <td class="col-lg-2">
                                                {{-- <a href="{{ route('ticket.detail') }}" class="btn btn-primary">Buy</a> --}}
                                                <a href="{{ route('match.detail', ['id' => $match->id]) }}"
                                                    class="btn btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
